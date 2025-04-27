from fastapi import FastAPI, HTTPException, Depends, Header
from pydantic import BaseModel
from typing import Optional, List
import aiomysql
import jwt
import hashlib
import datetime

app = FastAPI()

# Configuración
SECRET_KEY = "TU_CLAVE_SECRETA_AQUI"
DB_CONFIG = {
    'host': 'localhost',
    'port': 3306,
    'user': 'usuario_base_de_datos',
    'password': 'contraseña_base_de_datos',
    'db': 'nombre_base_de_datos'
}

# Modelos Pydantic
class LoginRequest(BaseModel):
    cuenta: str
    clave_acceso: str

class Medida(BaseModel):
    id: int
    zona: int
    fecha: datetime.datetime
    tension: Optional[float]
    peso: Optional[float]
    estructura: Optional[str]
    observaciones: Optional[str]

# Función para conexión a base de datos
async def get_db():
    conn = await aiomysql.connect(**DB_CONFIG)
    try:
        yield conn
    finally:
        conn.close()

# Función para verificar token
def verify_token(token: str):
    try:
        payload = jwt.decode(token, SECRET_KEY, algorithms=["HS256"])
        return payload
    except jwt.ExpiredSignatureError:
        raise HTTPException(status_code=401, detail="Token expirado.")
    except jwt.InvalidTokenError:
        raise HTTPException(status_code=401, detail="Token inválido.")

# Endpoint de login
@app.post("/login")
async def login(request: LoginRequest, db=Depends(get_db)):
    async with db.cursor(aiomysql.DictCursor) as cursor:
        await cursor.execute(
            "SELECT * FROM USUARIOS WHERE cuenta = %s AND clave_acceso = %s",
            (request.cuenta, hashlib.md5(request.clave_acceso.encode()).hexdigest())
        )
        user = await cursor.fetchone()

    if not user:
        raise HTTPException(status_code=401, detail="Credenciales inválidas.")

    payload = {
        "sub": user['id'],
        "cuenta": user['cuenta'],
        "exp": datetime.datetime.utcnow() + datetime.timedelta(hours=1)
    }
    token = jwt.encode(payload, SECRET_KEY, algorithm="HS256")

    return {"token": token}

# Endpoint para consultar medidas
@app.get("/medidas", response_model=List[Medida])
async def get_medidas(
    fecha_inicio: str,
    fecha_fin: str,
    tension_min: Optional[float] = None,
    tension_max: Optional[float] = None,
    peso_min: Optional[float] = None,
    peso_max: Optional[float] = None,
    authorization: str = Header(None),
    db=Depends(get_db)
):
    if not authorization or not authorization.startswith("Bearer "):
        raise HTTPException(status_code=401, detail="Token no proporcionado.")

    token = authorization.split(" ")[1]
    verify_token(token)

    query = """
        SELECT id, zona, fecha, tension, peso, estructura, observaciones
        FROM MEDIDAS
        WHERE fecha BETWEEN %s AND %s
    """
    params = [fecha_inicio, fecha_fin]

    if tension_min is not None:
        query += " AND tension >= %s"
        params.append(tension_min)
    if tension_max is not None:
        query += " AND tension <= %s"
        params.append(tension_max)
    if peso_min is not None:
        query += " AND peso >= %s"
        params.append(peso_min)
    if peso_max is not None:
        query += " AND peso <= %s"
        params.append(peso_max)

    query += " ORDER BY fecha ASC"

    async with db.cursor(aiomysql.DictCursor) as cursor:
        await cursor.execute(query, params)
        results = await cursor.fetchall()

    return results


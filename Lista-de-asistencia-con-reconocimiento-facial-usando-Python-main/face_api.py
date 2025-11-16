import face_recognition
import cv2
import numpy as np
import mysql.connector
from datetime import datetime
from flask import Flask, jsonify, request
from flask_cors import CORS
import threading

app = Flask(__name__)
CORS(app)

# Configuración de la base de datos
DB_CONFIG = {
    "host": "localhost",
    "user": "root",
    "password": "123456",
    "database": "asistencia"
}

# Cargar imágenes y encodings
KNOWN_USERS = [
    ("so.jpg", "so", "maestra"),
    ("me.jpg", "me", "estudiante"),
    ("sa.jpg", "sa", "estudiante"),
    ("va.jpg", "va", "estudiante")  # <-- Agrega esta línea
]
known_face_encodings = []
known_face_names = []
known_face_roles = []

print("Cargando imágenes de rostros...")
for fname, uname, rol in KNOWN_USERS:
    print(f"Intentando cargar {fname}...")
    try:
        img = face_recognition.load_image_file(fname)
        encs = face_recognition.face_encodings(img)
        if encs:
            known_face_encodings.append(encs[0])
            known_face_names.append(uname)
            known_face_roles.append(rol)
            print(f"Rostro detectado en {fname}")
        else:
            print(f"[WARN] No se detectó rostro en {fname}")
    except Exception as e:
        print(f"[WARN] No se pudo cargar {fname}: {e}")

print("Total de rostros cargados:", len(known_face_encodings))
if not known_face_encodings:
    raise SystemExit("No se cargaron encodings de caras. Asegúrate de tener so.jpg, me.jpg, sa.jpg con un rostro visible.")

# Función para registrar asistencia
def registrar_asistencia(nombre):
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()
        fecha = datetime.now().date()
        hora = datetime.now().time().strftime("%H:%M:%S")
        # Verifica si ya tiene asistencia hoy
        cursor.execute("SELECT id FROM registro WHERE nombre=%s AND fecha=%s", (nombre, fecha))
        if cursor.fetchone():
            estado = "ya registrada"
        else:
            cursor.execute("INSERT INTO registro (fecha, nombre, hora) VALUES (%s, %s, %s)", (fecha, nombre, hora))
            conn.commit()
            estado = "asistencia registrada"
        cursor.close()
        conn.close()
        return estado
    except Exception as e:
        return f"error: {e}"

# Endpoint para detección facial
@app.route('/detectar', methods=['POST'])
def detectar():
    print("Solicitud recibida en /detectar")  # <--- Agrega esto
    try:
        # Espera una imagen en base64 en el campo 'face_image'
        data = request.json
        if not data or 'face_image' not in data:
            return jsonify({"error": "No se recibió imagen"}), 400

        import base64
        img_data = data['face_image']
        img_data = img_data.replace('data:image/jpeg;base64,', '').replace(' ', '+')
        img_bytes = base64.b64decode(img_data)
        nparr = np.frombuffer(img_bytes, np.uint8)
        frame = cv2.imdecode(nparr, cv2.IMREAD_COLOR)
        if frame is None:
            return jsonify({"error": "Imagen inválida"}), 400

        # Convertir a RGB
        rgb_frame = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
        face_locations = face_recognition.face_locations(rgb_frame)
        face_encodings = face_recognition.face_encodings(rgb_frame, face_locations)

        if not face_encodings:
            return jsonify({"error": "No se detectó ningún rostro"}), 400

        for face_encoding in face_encodings:
            matches = face_recognition.compare_faces(known_face_encodings, face_encoding)
            face_distances = face_recognition.face_distance(known_face_encodings, face_encoding)
            best_match_index = np.argmin(face_distances)
            if matches[best_match_index]:
                usuario = known_face_names[best_match_index]
                rol = known_face_roles[best_match_index]
                if rol == "maestra":
                    return jsonify({"usuario": usuario, "rol": rol, "estado": "inicio de sesión exitoso"})
                else:
                    estado = registrar_asistencia(usuario)
                    return jsonify({"usuario": usuario, "rol": rol, "estado": estado})
        return jsonify({"error": "Rostro no reconocido"}), 401
    except Exception as e:
        return jsonify({"error": str(e)}), 500

def run_flask():
    app.run(host="0.0.0.0", port=5000, debug=False)

if __name__ == "__main__":
    print("Iniciando API de reconocimiento facial en http://localhost:5000 ...")
    run_flask()
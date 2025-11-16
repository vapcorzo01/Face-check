import face_recognition
import cv2
import numpy as np
import time
import os

video_capture = None
for i in range(4):  # prueba índices 0..3
    cap = cv2.VideoCapture(i, cv2.CAP_DSHOW)
    if cap.isOpened():
        video_capture = cap
        print(f"Usando cámara index {i} (CAP_DSHOW)")
        break

if video_capture is None:
    # fallback simple
    video_capture = cv2.VideoCapture(0)
    print("No se pudo abrir con CAP_DSHOW, intentando VideoCapture(0)")

video_capture.set(cv2.CAP_PROP_FRAME_WIDTH, 640)
video_capture.set(cv2.CAP_PROP_FRAME_HEIGHT, 480)

known_face_encodings = []
known_face_names = []

# Puedes poner varias imágenes por persona en la lista (mejorará robustez)
images_to_load = [
    (["so.jpg"], "So"),
    (["sa.jpg"], "Sa"),
    (["me.jpg"], "Me"),
    (["va.jpg"], "Ca")   # corrige "va,jpg" -> "va.jpg" y etiqueta "Ca"
]

for fnames, display_name in images_to_load:
    encs_person = []
    for fname in fnames:
        path = os.path.join(os.path.dirname(__file__), fname)
        try:
            img = face_recognition.load_image_file(path)
        except FileNotFoundError:
            print(f"Archivo no encontrado: {path} — omitiendo")
            continue

        encs = face_recognition.face_encodings(img)
        if not encs:
            print(f"No se detectó ninguna cara en {fname} — omitiendo")
            continue

        encs_person.append(encs[0])

    if encs_person:
        # promediar encodings si hay más de 1 imagen por persona
        avg_enc = np.mean(encs_person, axis=0)
        known_face_encodings.append(avg_enc)
        known_face_names.append(display_name)

if not known_face_encodings:
    raise SystemExit("No se cargaron encodings de caras. Revisa los archivos de imagen y que contengan una cara visible.")

# Initialize some variables
face_locations = []
face_encodings = []
face_names = []
process_this_frame = True

# Mejor: procesar solo cada N frames
PROCESS_EVERY_N_FRAMES = 3
_frame_counter = 0

# Ajusta la tolerancia: 0.6 es por defecto; baja para ser más estricto (p.ej. 0.45-0.5)
TOLERANCE = 0.5

while True:
    # Grab a single frame of video
    ret, frame = video_capture.read()

    # Verificar que se leyó un frame válido
    if not ret or frame is None:
        print("Advertencia: no se pudo leer frame desde la cámara. Reintentando...")
        time.sleep(0.2)
        continue

    _frame_counter += 1
    # Only process every N-th frame of video to save time
    if _frame_counter % PROCESS_EVERY_N_FRAMES == 0:
        # Resize frame of video to 1/4 size (o ajusta) for faster face recognition processing
        # Si ya redujiste la resolución de la cámara, puedes usar fx=0.5 o incluso 1.0
        small_frame = cv2.resize(frame, (0, 0), fx=0.25, fy=0.25, interpolation=cv2.INTER_LINEAR)

        # Convert the image from BGR color (which OpenCV uses) to RGB color (which face_recognition uses)
        rgb_small_frame = cv2.cvtColor(small_frame, cv2.COLOR_BGR2RGB)
        
        # --- Optimización: usar 'hog' (rápido en CPU) y reducir num_jitters ---
        face_locations = face_recognition.face_locations(rgb_small_frame, model='hog')
        if face_locations:
            face_encodings = face_recognition.face_encodings(rgb_small_frame, face_locations, num_jitters=1)
        else:
            face_encodings = []

        face_names = []
        for face_encoding in face_encodings:
            name = "Unknown"
            if len(known_face_encodings) > 0:
                face_distances = face_recognition.face_distance(known_face_encodings, face_encoding)
                best_match_index = np.argmin(face_distances)
                best_distance = face_distances[best_match_index]
                # asigna nombre solo si la distancia es menor que la tolerancia
                if best_distance < TOLERANCE:
                    name = known_face_names[best_match_index]
                # opcional: imprimir distancias para depuración
                print(f"Distancias: {face_distances}, mejor={best_distance:.3f} -> {name}")

            face_names.append(name)

    process_this_frame = not process_this_frame

    # Display the results
    for (top, right, bottom, left), name in zip(face_locations, face_names):
        # Scale back up face locations since the frame we detected in was scaled to 1/4 size
        top *= 4
        right *= 4
        bottom *= 4
        left *= 4

        # Draw a box around the face
        cv2.rectangle(frame, (left, top), (right, bottom), (0, 0, 255), 2)

        # Draw a label with a name below the face
        cv2.rectangle(frame, (left, bottom - 35), (right, bottom), (0, 0, 255), cv2.FILLED)
        font = cv2.FONT_HERSHEY_DUPLEX
        cv2.putText(frame, name, (left + 6, bottom - 6), font, 1.0, (255, 255, 255), 1)

    # Display the resulting image
    cv2.imshow('Video', frame)

    # Hit 'q' on the keyboard to quit!
    if cv2.waitKey(1) & 0xFF == ord('q'):
        break

# Release handle to the webcam
video_capture.release()
cv2.destroyAllWindows()

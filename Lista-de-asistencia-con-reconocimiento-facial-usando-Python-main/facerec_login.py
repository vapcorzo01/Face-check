import face_recognition
import sys
import numpy as np
import os

known_face_encodings = []
known_face_names = []

images_to_load = [
    (["so.jpg"], "So"),
    (["sa.jpg"], "Sa"),
    (["me.jpg"], "Me")
]

for fnames, display_name in images_to_load:
    encs_person = []
    for fname in fnames:
        path = os.path.join(os.path.dirname(__file__), fname)
        try:
            img = face_recognition.load_image_file(path)
        except FileNotFoundError:
            continue
        encs = face_recognition.face_encodings(img)
        if encs:
            encs_person.append(encs[0])
    if encs_person:
        avg_enc = np.mean(encs_person, axis=0)
        known_face_encodings.append(avg_enc)
        known_face_names.append(display_name)

if len(sys.argv) < 2:
    print("")
    sys.exit(1)

test_image_path = sys.argv[1]
test_image = face_recognition.load_image_file(test_image_path)
face_locations = face_recognition.face_locations(test_image, model='hog')
face_encodings = face_recognition.face_encodings(test_image, face_locations)

TOLERANCE = 0.6  # Puedes ajustar este valor

for face_encoding in face_encodings:
    face_distances = face_recognition.face_distance(known_face_encodings, face_encoding)
    best_match_index = np.argmin(face_distances)
    best_distance = face_distances[best_match_index]
    if best_distance < TOLERANCE:
        print(known_face_names[best_match_index])
        sys.exit(0)

print("")
sys.exit(1)
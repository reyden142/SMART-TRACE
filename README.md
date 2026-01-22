## SMART-TRACE Thesis Workspace

> **SMART-TRACE: A COMBINED RFID DATABASING AND WIFI-RSSI FINGERPRINTING INDOOR POSITIONING FOR DISASTER MANAGEMENT USING SMARTPHONE**

🎯 This repository contains the full working materials for the SMART-TRACE thesis: backend, frontend, data collection, machine learning notebooks, and hardware experiments.

**📄 Research paper:** The published research is accessible at [ResearchGate](https://www.researchgate.net/publication/399961256_SMART-TRACE_A_COMBINED_RFID_DATABASING_AND_WIFI-RSSI_FINGERPRINTING_INDOOR_POSITIONING_FOR_DISASTER_MANAGEMENT_USING_SMARTPHONE).

![Python](https://img.shields.io/badge/Python-3.x-3776AB?style=flat&logo=python&logoColor=white)
![Jupyter](https://img.shields.io/badge/Jupyter-Notebook-F37626?style=flat&logo=jupyter&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-7.3+-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=flat&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-3.4-7952B3?style=flat&logo=bootstrap&logoColor=white)
![jQuery](https://img.shields.io/badge/jQuery-2.2.3-0769AD?style=flat&logo=jquery&logoColor=white)
![Leaflet](https://img.shields.io/badge/Leaflet-1.9.4-199900?style=flat&logo=leaflet&logoColor=white)
![MapTiler](https://img.shields.io/badge/MapTiler-API-FF6B6B?style=flat)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?style=flat&logo=javascript&logoColor=black)
![HTML/CSS](https://img.shields.io/badge/HTML%2FCSS-5%20%7C%203-E34F26?style=flat&logo=html5&logoColor=white)
![C++](https://img.shields.io/badge/C%2B%2B-17-00599C?style=flat&logo=cplusplus&logoColor=white)

---

### 🔍 High-Level Overview

- 📡 **Indoor positioning** using WiFi RSSI fingerprinting
- 🪪 **RFID databasing** for tracking and identification
- 📱 **Smartphone-based client** for disaster management scenarios
- 🌐 **Web + API backend** (Django + PHP)
- 🧠 **Machine learning** (KNN-based fingerprinting and validation)
- 📊 **CAPsMAN (MikroTik)** used for centralized WiFi AP management and **RSSI data acquisition**

---

### ✨ Features

- **WiFi RSSI fingerprinting** — Indoor location estimation using received signal strength from multiple access points
- **RFID databasing** — Track and identify tagged persons or assets via RFID readers
- **Disaster management focus** — Smartphone-based client for evacuation, triage, and situational awareness
- **KNN-based ML positioning** — Train and run K-Nearest Neighbors models for fingerprint matching
- **Interactive maps** — Leaflet + MapTiler for indoor maps and real-time position visualization
- **REST APIs** — Position scanning and RSSI endpoints for integration with apps and scripts
- **CAPsMAN integration** — Use MikroTik CAPsMAN for centralized AP management and RSSI data collection
- **Jupyter notebooks** — Data gathering, KNN analysis, and experimentation workflows
- **Django + PHP stack** — Web backend (Django) and RFID Indoor Positioning System frontend (PHP/MySQL)

---

### 🧱 Project Structure (Simplified)

```text
Thesis2.0/
├── backend/          # Django project (web app + API integration)
├── frontend/         # PHP web frontend (RFID Indoor Positioning System)
├── notebooks/        # Jupyter notebooks (data gathering + KNN analysis)
├── algorithms/       # KNN algorithm implementations + training datasets
├── data_collection/  # WiFi RSSI & position scanning scripts (CAPsMAN-based)
├── data/             # Raw, processed, and final datasets
├── hardware/         # Arduino / ESP-IDF / drivers
├── api/              # REST endpoints and Django API glue code
├── docs/             # Documentation (e.g., dataset PDF)
└── archive/          # Old / experimental / backup copies
```
---

### 📡 Data Collection & CAPsMAN (MikroTik)

- WiFi RSSI data is acquired using **MikroTik CAPsMAN**:
  - Centralized management of multiple CAPs (APs)
  - Periodic scanning of RSSI values at different reference points
  - Exported logs and CSVs processed into fingerprint datasets
- Related assets:
  - `data_collection/wifi/` – users log trials, AP data, calibration CSVs
  - `data_collection/position_scanner/` – position scanning scripts + captured RSSI
  - `data/dataset/Data Gathering (Thesis 2)/` – curated datasets derived from CAPsMAN logs

---

### 📓 Notebooks & Machine Learning

- **Data gathering notebooks**
  - `notebooks/data_gathering/Data Gathering Sample Size = 100.ipynb`
  - `notebooks/data_gathering/Data Gathering Sample Size = 200.ipynb`
- **KNN analysis / fingerprinting notebooks**
  - `notebooks/knn_analysis/` (multiple iterations and final versions)
  - Datasets and intermediate CSVs: `notebooks/data/` and `data/dataset/`
- Machine learning artifacts:
  - Trained models / joblib files (e.g. `MLKNN_1_classifier.joblib`)

---

### 🧮 Algorithms

- KNN fingerprinting implementations in `algorithms/knn/`:
  - Multiple versions (`KNN_Algorithm*.py`) for experimentation
  - Training datasets (`trainingdataset/*.csv`)
  - Aggregated prediction outputs and evaluation CSVs
- Integrated KNN usage also appears inside the Django `myapp` (e.g. `KNN_Algorithm.py`).

---

### 🌐 Backend & APIs

- **Django backend** (`backend/django_thesis/`)
  - Handles web views, models, admin, and integration with KNN logic
  - Uses both SQLite (development) and MySQL (production-style config)
- **APIs**
  - `api/django_api/` – Django-based API integration (`api.py`)
  - `api/rest_api/` – standalone Python scripts for scanning endpoints:
    - `Position_Scanner_cap*.py`
    - `Scanned_API_cap*.py`

---

### 🚀 Getting Started

**Prerequisites:** Python 3.x, PHP 7.3+, MySQL 5.7+, and (optional) Jupyter for notebooks. For RSSI data collection, a MikroTik CAPsMAN setup is required.

1. **Clone the repository**
   ```bash
   git clone https://github.com/reyden142/Thesis2.0.git
   cd Thesis2.0
   ```

2. **Django backend**
   - Create and activate a virtual environment, then install Django and project dependencies.
   - From `backend/django_thesis/`:
     ```bash
     python manage.py migrate
     python manage.py runserver
     ```
   - The app runs at `http://127.0.0.1:8000/` (or the URL shown). Uses SQLite by default; configure MySQL in `settings.py` if needed.

3. **PHP frontend (RFID Indoor Positioning System)**
   - Use XAMPP, WAMP, or a similar stack with PHP and MySQL.
   - Place the `frontend/rfid_ips` files in your web server document root (e.g. `htdocs`).
   - Import any provided SQL schemas (e.g. `rfid_ips.sql`, `ap_data.sql`) into MySQL and configure DB credentials in the PHP app.
   - Access the frontend via your local URL (e.g. `http://localhost/rfid_ips/`).

4. **APIs and data collection**
   - Use the scripts in `api/rest_api/` and `data_collection/position_scanner/` to scan positions and consume RSSI/position endpoints. Ensure the Django server (and CAPsMAN, if used) are running where applicable.

5. **Notebooks and ML**
   - Open Jupyter from the project root or `notebooks/`, then run the data gathering and KNN analysis notebooks as needed. Datasets live in `data/dataset/` and `notebooks/data/`.

---

### 📄 License

This project is licensed under the **MIT License**:

```text
MIT License

Copyright (c) 2024 Reyden Jenn Cagata

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
```

---

## 👥 Authors

- **Reyden Jenn Cagata** - *Initial work* - [reyden142](https://github.com/reyden142)
- **Edwin Jr. Ligan** - [JMazeikeen](https://github.com/JMazeikeen)
- **Shen Wa Lai** 

## 🙏 Acknowledgments

- University of Mindanao - College of Engineering Education, Electronics Engineering
- Bootstrap team for the CSS framework
- jQuery team for the JavaScript library
- All contributors and users of this system

## 📞 Support

For support, email reydencagata@gmail.com or open an issue in the repository.

---

Thank you for your interest in **SMART-TRACE**. We hope this system contributes to safer, more efficient disaster management and indoor positioning research.


# CSV Sales Report App

---

## 🇵🇱 Opis

Prosta aplikacja dashboardowa oparta o Laravel (PHP) oraz Vue (Inertia.js).  
Dane pochodzą wyłącznie z pliku CSV – bez użycia bazy danych.

---

## 🇬🇧 Overview

A simple dashboard application built with Laravel (PHP) and Vue (Inertia.js).  
All data is loaded directly from a CSV file – no database is used.

---

## 🚀 Technologie / Tech Stack

- Laravel (PHP)
- Inertia.js
- Vue 3
- Tailwind CSS
- Docker

---

## 📊 Funkcjonalności / Features

- Lista wydarzeń z agregacją sprzedaży biletów (status = confirmed)
- Ranking TOP 10 kampanii UTM
- Filtrowanie (miasto, zakres dat, kategoria)
- Obsługa wielu języków (PL / EN)
- Dashboard z podstawowymi statystykami (KPI)
- Sortowanie i paginacja danych

---

## 🧠 Architektura / Architecture

Projekt został podzielony zgodnie z zasadą separacji odpowiedzialności:

- **CsvSalesReader** – odczyt danych z pliku CSV
- **SalesReportService** – logika biznesowa (agregacja, ranking, statystyki)
- **DashboardPageDataBuilder** – przygotowanie danych do widoku
- **Vue Components** – warstwa prezentacji (UI)

---

## 📁 Lokalizacja pliku CSV

```text
storage/app/data/sales.csv
```

## ⚙️ Uruchomienie / Setup

```bash
git clone https://github.com/wlazmate/csv-sales-report-app.git
cd csv-sales-report-app

cp .env.example .env

docker compose up -d --build

docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose run --rm npm npm install
docker compose run --rm npm npm run build
```

### Dostęp do aplikacji:
- http://localhost:8000

## 🛠️ Naprawa błędów / Fix permissions
Jeżeli podczas instalacji npm install wystąpi błąd / If you encounter permission issues during npm install:
```bash
sudo chown -R $USER:$USER .
```

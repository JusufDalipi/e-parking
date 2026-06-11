# 🅿️ E-Parking — Sistem Menaxhimi Parkingu

Sistem web për menaxhimin e parkingut, ndërtuar me **PHP**, **MySQL**, **JavaScript** dhe **CSS**. Mbështet role të shumta, rezervime vendesh, gjurmim hyrje-daljeje, pagesa dhe raporte XML.

---

## 🚀 Teknologjitë

| Teknologjia | Përdorimi |
|---|---|
| PHP 8+ | Backend & logika e serverit |
| MySQL | Databaza relacionale |
| JavaScript | Interaktivitet & animacione |
| CSS | Stilizim & dizajn responsive |
| XAMPP | Server lokal për zhvillim |
| XML | Eksportimi i të dhënave |
| JWT | Autentifikim i sigurt |

---

## 👥 Rolet e Sistemit

| Roli | Përshkrimi |
|---|---|
| 🔑 **Admin** | Kontrollon gjithçka — përdoruesit, vendet, pagesat, raportet |
| 🛡️ **Rojtar** | Monitoron hyrje-dalje dhe statusin e vendeve në kohë reale |
| 🚗 **Shofer** | Bën rezervime dhe shikon pagesat e tij |

---

## ✨ Funksionalitetet

- 📊 **Dashboard interaktiv** me statistika live dhe animacione
- 🅿️ **Menaxhim vendesh** — shto, edito, fshi vende parkingu
- 📋 **Rezervime** — shoferët rezervojnë vende specifike
- 🚗 **Hyrje-Dalje** — rojtarët regjistrojnë hyrjet dhe daljet e mjeteve
- 💳 **Pagesa** — gjurmim i pagesave me histori të plotë
- 📦 **Abonime** — planet e abonimit për shoferë
- 👤 **Menaxhim Përdoruesish** — CRUD i plotë me role dhe status
- 🔐 **Regjistrim & Login** — autentifikim me sesione dhe JWT
- 📋 **Logje login-i** — histori e plotë e hyrjeve në sistem
- 📤 **XML Eksport** — eksportim i pagesave në format XML
- ⚠️ **Njoftime** — admin njoftohet për fjalëkalime të harruara

---

## 📂 Struktura e Projektit

```
e-parking/
├── php/
│   ├── config.php          # Konfigurimi i databazës
│   ├── auth.php            # Autentifikimi & autorizimi
│   ├── header.php          # Navigacioni kryesor
│   ├── jwt.php             # JWT helper
│   └── ...
├── css/                    # Stilet CSS
├── js/                     # Skriptet JavaScript
├── assets/                 # Ikonat dhe imazhet
├── sql/                    # Skema e databazës
├── xml/                    # Eksporti XML
├── index.php               # Dashboard kryesor
├── login.php               # Faqja e hyrjes
├── users.php               # Menaxhimi i përdoruesve
├── slots.php               # Menaxhimi i vendeve
├── reservations.php        # Rezervimet
├── entries.php             # Hyrje-Dalje mjetesh
├── payments.php            # Pagesat
├── subscriptions.php       # Abonimi
├── roles.php               # Rolet e sistemit
└── login_logs.php          # Logjet e hyrjeve
```

---

## ⚙️ Instalimi Lokal

### Kërkesat
- [XAMPP](https://www.apachefriends.org/) (PHP 8+ & MySQL)
- Shfletues modern (Chrome, Firefox, Edge)

### Hapat

**1. Klono repo-n:**
```bash
git clone https://github.com/JusufDalipi/e-parking.git
cd e-parking
```

**2. Vendos projektin:**
Kopjo folderin në `c:\xampp\htdocs\`

**3. Importo databazën:**
- Hap [phpMyAdmin](http://localhost/phpmyadmin)
- Krijo databazë të re: `parking_management`
- Importo skedarin: `sql/parking_management.sql`

**4. Konfiguro lidhjen:**
Edito skedarin `php/config.php`:
```php
$host   = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'parking_management';
```

**5. Hap aplikacionin:**
```
http://localhost/e-parking/
```

---

## 🔐 Kredencialet Default

| Roli | Email | Fjalëkalimi |
|---|---|---|
| Admin | admin@parking.com | *(vendos gjatë setup)* |

---

## 📤 Eksport XML

Pagesat mund të eksportohen në format XML duke shkuar te:
```
/xml/export_payments.php
```

---

## 🗄️ Databaza

Projekti përdor **MySQL** me tabelat kryesore:
- `users` — Përdoruesit
- `roles` / `user_roles` — Rolet
- `parking_slots` — Vendet e parkingut
- `reservations` — Rezervimet
- `vehicle_entries` — Hyrje-dalje mjetesh
- `payments` — Pagesat
- `subscriptions` — Abonimi
- `login_logs` — Logjet e hyrjeve
- `password_resets` — Kërkesat për reset fjalëkalimi

---

## 👨‍💻 Autori

**Jusuf Dalipi**  
🔗 [github.com/JusufDalipi](https://github.com/JusufDalipi)

---

## 📄 Licenca

Ky projekt është ndërtuar për qëllime akademike.

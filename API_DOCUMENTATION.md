# API Documentation

Base URL: `http://localhost/top_ummarhuk/api` (or your production domain)

## 1. Umrah Packages

### Get All Packages
Fetch a list of all Umrah packages along with their details (cities and hotels).

- **URL:** `/ummarh-packages`
- **Method:** `GET`
- **Headers:** None required
- **Body:** None required

**Success Response (200 OK):**
```json
{
    "status": true,
    "message": "Packages fetched successfully",
    "data": [ ... ]
}
```

---

## 2. Bookings (Flight, Hotel, Custom Umrah)

### A. Flight Booking
Submit a flight booking request.

- **URL:** `/flight-booking`
- **Method:** `POST`
- **Headers:** `Content-Type: application/json`, `Accept: application/json`
- **Body:**
```json
{
    "trip_type": "One Way / Round Trip (String, max 50)",
    "departure": "City/Airport (String, max 100)",
    "destination": "City/Airport (String, max 100)",
    "departure_date": "YYYY-MM-DD (Date, today or later)",
    "return_date": "YYYY-MM-DD (Date, after departure_date, optional)",
    "cabin_class": "Economy/Business/First (String, max 50)",
    "airline": "Airline Name (String, max 100)",
    "adults": 2, // (Integer, min 1)
    "children": 1, // (Integer, min 0, optional)
    "infants": 0, // (Integer, min 0, optional)
    "name": "Full Name (String, max 255)",
    "email": "user@example.com (String, max 255)",
    "mobile": "1234567890 (String, max 20)"
}
```

**Success Response (200 OK):**
```json
{
    "status": true,
    "message": "Flight booking created successfully",
    "data": { ... }
}
```

### B. Hotel Booking
Submit a hotel booking request.

- **URL:** `/hotel-booking`
- **Method:** `POST`
- **Headers:** `Content-Type: application/json`, `Accept: application/json`
- **Body:**
```json
{
    "name": "Full Name (String, max 255)",
    "email": "user@example.com (String, max 255)",
    "mobile": "1234567890 (String, max 20)",
    "destination": "City Name (String, max 255)",
    "check_in_date": "YYYY-MM-DD (Date, today or later)",
    "check_out_date": "YYYY-MM-DD (Date, after check_in_date)",
    "rooms": 1, // (Integer, 1-10)
    "adults": 2, // (Integer, 1-10)
    "children": 0 // (Integer, 0-10, optional)
}
```

**Success Response (200 OK):**
```json
{
    "status": true,
    "message": "Hotel booking created successfully",
    "data": { ... }
}
```

### C. Custom Umrah Quote
Submit a custom Umrah quote request.

- **URL:** `/custom-umrah-quote`
- **Method:** `POST`
- **Headers:** `Content-Type: application/json`, `Accept: application/json`
- **Body:**
```json
{
    "name": "Full Name (String, max 100)",
    "phone_number": "1234567890 (String, max 20)",
    "email": "user@example.com (String, max 150)",
    "no_of_passengers": 2, // (Integer, min 1)
    "travel_date": "YYYY-MM-DD (Date)",
    "makkah_nights": 5, // (Integer, min 0)
    "madinah_nights": 5, // (Integer, min 0)
    "accommodation": "3 Star / 4 Star / 5 Star (String, max 100)"
}
```

**Success Response (201 Created):**
```json
{
    "success": true,
    "message": "Umrah quote request successfully submit ho gayi!",
    "data": { "id": 1 }
}
```

---

## 3. Transport Booking

### A. Get All Transport Routes
Fetch all distinct transport routes available.

- **URL:** `/transport/routes`
- **Method:** `GET`
- **Headers:** None required

**Success Response (200 OK):**
```json
{
    "success": true,
    "routes": [ "Makkah to Madinah", "Jeddah to Makkah", ... ]
}
```

### B. Get Vehicles By Route
Fetch vehicles and their rates for a specific route.

- **URL:** `/transport/vehicles?route={route_name}`
- **Method:** `GET`
- **Headers:** None required
- **Query Parameters:**
  - `route` (required): Name of the route (e.g., `Makkah to Madinah`)

**Success Response (200 OK):**
```json
{
    "success": true,
    "vehicles": [
        {
            "vehicle": "GMC",
            "rate_per_passenger": 150
        },
        ...
    ]
}
```

### C. Get Transport Rate
Fetch the rate per passenger for a specific route and vehicle.

- **URL:** `/transport/rate?route={route_name}&vehicle={vehicle_name}`
- **Method:** `GET`
- **Headers:** None required
- **Query Parameters:**
  - `route` (required): Name of the route
  - `vehicle` (required): Name of the vehicle

**Success Response (200 OK):**
```json
{
    "success": true,
    "rate_per_passenger": 150
}
```

### D. Submit Transport Booking
Submit a transport booking request.

- **URL:** `/transport/booking`
- **Method:** `POST`
- **Headers:** `Content-Type: application/json`, `Accept: application/json`
- **Body:**
```json
{
    "route": "Makkah to Madinah (String, max 150)",
    "vehicle": "GMC (String, max 100)",
    "passengers": 4 // (Integer, min 1)
}
```

**Success Response (201 Created):**
```json
{
    "success": true,
    "message": "Booking successfully ho gayi!",
    "data": {
        "booking_id": 1,
        "route": "Makkah to Madinah",
        "vehicle": "GMC",
        "passengers": 4,
        "rate_per_passenger": "150 Riyal",
        "total_amount": "600 Riyal"
    }
}
```

<!DOCTYPE html>
<html>
<head><title>Staff Dashboard</title></head>
<body>
    <h1>Welcome, staff!</h1>
    <form method="POST" action="/logout">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>
</html>
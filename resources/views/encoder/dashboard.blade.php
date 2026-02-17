<!DOCTYPE html>
<html>
<head><title>Encoder Dashboard</title></head>
<body>
    <h1>Welcome, encoder!</h1>
    <form method="POST" action="/logout">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>
</html>
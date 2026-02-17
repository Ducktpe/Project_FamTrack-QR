<!DOCTYPE html>
<html>
<head><title>Auditor Dashboard</title></head>
<body>
    <h1>Welcome, auditor!</h1>
    <form method="POST" action="/logout">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <title>Case Update</title>
</head>
<body>
    <p>Hello Admin,</p>
    <p><b>{{$name}}</b> updated case fields lists are below:</p>
    @foreach($updatedCaseFields as $key => $field)
        <p>{{$key}} : {{$field}}</p>
    @endforeach
</body>
</html>

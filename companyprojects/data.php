<?php include "db.php"; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Submitted Data</title>
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #4751a6ff, #c84a72ff);
            margin: 0;
            padding: 40px;
        }

        h2 {
            text-align: center;
            color: #fff;
            font-size: 32px;
            margin-bottom: 30px;
            letter-spacing: 1px;
        }

        table {
            width: 90%;
            margin: auto;
            border-collapse: collapse;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }

        th {
            background: rgba(255,255,255,0.2);
            color: #fff;
            padding: 15px;
            font-size: 18px;
        }

        td {
            padding: 15px;
            color: #fff;
            font-size: 16px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        img {
            width: 70px;
            height: 70px;
            border-radius: 8px;
            border: 2px solid #fff;
        }

        .yes-btn {
            padding: 10px 20px;
            border: none;
            background: #00ff88;
            color: #000;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }

        .yes-btn:hover {
            background: #00dd77;
            transform: scale(1.05);
        }

        .approved {
            color: #00ff88;
            font-weight: bold;
        }
    </style>

</head>
<body>

<h2>Submitted User Data</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Photo</th>
        <th>Name</th>
        <th>Mobile</th>
        <th>Bank</th>
        <th>Status</th>
    </tr>

<?php
$res = mysqli_query($conn, "SELECT * FROM users");
while($row = mysqli_fetch_assoc($res)){
    echo "<tr>
        <td>".$row['id']."</td>
        <td><img src='uploads/".$row['photo']."'></td>
        <td>".$row['full_name']."</td>
        <td>".$row['mobile']."</td>
        <td>".$row['bank_name']."</td>
        <td>";

    if ($row['approved'] == 'yes') {
        echo "<span class='approved'>✔ Approved</span>";
    } else {
        echo "
        <form action='update_status.php' method='POST'>
            <input type='hidden' name='id' value='".$row['id']."'>
            <button class='yes-btn'>YES</button>
        </form>";
    }

    echo "</td></tr>";
}
?>

</table>

</body>
</html>

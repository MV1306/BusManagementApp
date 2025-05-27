<!DOCTYPE html>
<html>
<head>
    <title>Fare Chart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f7f7f7;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        /* Tab container */
        .tab {
            overflow: hidden;
            border-bottom: 2px solid #ccc;
            margin-bottom: 20px;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }
        /* Tab buttons */
        .tab button {
            background-color: #e7e7e7;
            float: left;
            border: none;
            outline: none;
            cursor: pointer;
            padding: 12px 20px;
            transition: 0.3s;
            font-size: 16px;
            margin-right: 4px;
            border-radius: 6px 6px 0 0;
        }
        .tab button:hover {
            background-color: #d0d0d0;
        }
        .tab button.active {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        /* Tab content */
        .tabcontent {
            display: none;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
            background: white;
            border: 1px solid #ccc;
            border-top: none;
            padding: 20px;
            border-radius: 0 6px 6px 6px;

            /* Flex container for tables */
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }
        table {
            width: 30%; /* three tables side by side */
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <div> <?php include 'navbar.php'; ?> </div>
<h2>Fare Chart by Bus Type</h2>

<div class="tab">
    <button class="tablinks active" onclick="openTab(event, 'Ordinary')">Ordinary</button>
    <button class="tablinks" onclick="openTab(event, 'Express')">Express</button>
    <button class="tablinks" onclick="openTab(event, 'Deluxe')">Deluxe</button>
    <button class="tablinks" onclick="openTab(event, 'AC')">A/C</button>
    <button class="tablinks" onclick="openTab(event, 'Night')">Night Service</button>
</div>

<!-- Ordinary Tab -->
<div id="Ordinary" class="tabcontent" style="display:flex;">
    <table id="ordinaryTable1"></table>
    <table id="ordinaryTable2"></table>
    <table id="ordinaryTable3"></table>
</div>

<!-- Express Tab -->
<div id="Express" class="tabcontent">
    <table id="expressTable1"></table>
    <table id="expressTable2"></table>
    <table id="expressTable3"></table>
</div>

<!-- Deluxe Tab -->
<div id="Deluxe" class="tabcontent">
    <table id="deluxeTable1"></table>
    <table id="deluxeTable2"></table>
    <table id="deluxeTable3"></table>
</div>

<!-- A/C Tab -->
<div id="AC" class="tabcontent">
    <table id="acTable1"></table>
    <table id="acTable2"></table>
    <table id="acTable3"></table>
</div>

<!-- Night Service Tab -->
<div id="Night" class="tabcontent">
    <table id="nightTable1"></table>
    <table id="nightTable2"></table>
    <table id="nightTable3"></table>
</div>

<script>
    // Sample fare data for 30 stages per bus type (replace with real data)
    const fareData = {
        ordinary: [
        5.00, 6.00, 7.00, 8.00, 9.00, 10.00, 11.00, 12.00, 13.00, 14.00,
        15.00, 15.00, 16.00, 16.00, 17.00, 17.00, 18.00, 18.00, 19.00, 19.00,
        20.00, 20.00, 21.00, 21.00, 22.00, 22.00, 23.00, 23.00, 24.00, 24.00
    ].map((fare, i) => ({ stage: i + 1, fare: fare })),
        
		express: [
        7.00, 9.00, 10.00, 12.00, 13.00, 15.00, 16.00, 18.00, 19.00, 21.00,
        22.00, 22.00, 24.00, 24.00, 25.00, 25.00, 27.00, 27.00, 28.00, 28.00,
        30.00, 30.00, 31.00, 31.00, 33.00, 33.00, 34.00, 34.00, 35.00, 35.00
    ].map((fare, i) => ({ stage: i + 1, fare: fare })),
	
	    deluxe: [
        11.00, 13.00, 15.00, 17.00, 19.00, 21.00, 23.00, 25.00, 27.00, 29.00,
        31.00, 31.00, 33.00, 33.00, 35.00, 35.00, 37.00, 37.00, 39.00, 39.00,
        41.00, 41.00, 43.00, 43.00, 45.00, 45.00, 47.00, 47.00, 49.00, 49.00
    ].map((fare, i) => ({ stage: i + 1, fare })),
	
	    ac: [
        15.00, 15.00, 20.00, 20.00, 20.00, 30.00, 30.00, 30.00, 40.00, 40.00,
        40.00, 40.00, 40.00, 40.00, 50.00, 50.00, 50.00, 50.00, 60.00, 60.00,
        60.00, 60.00, 70.00, 70.00, 70.00, 70.00, 70.00, 80.00, 80.00, 80.00
    ].map((fare, i) => ({ stage: i + 1, fare })),
	
        night: [
        11.00, 13.00, 15.00, 17.00, 19.00, 21.00, 23.00, 25.00, 27.00, 29.00,
        31.00, 31.00, 33.00, 33.00, 35.00, 35.00, 37.00, 37.00, 39.00, 39.00,
        41.00, 41.00, 43.00, 43.00, 45.00, 45.00, 47.00, 47.00, 49.00, 49.00
    ].map((fare, i) => ({ stage: i + 1, fare }))
    };

    // Function to create table HTML for a range of stages
    function createTableHtml(data, startIndex, endIndex) {
        let html = `<thead>
                        <tr>
                            <th>Stage(s) Travelled</th>
                            <th>Fare (₹)</th>
                        </tr>
                    </thead>
                    <tbody>`;

        for(let i = startIndex; i < endIndex && i < data.length; i++) {
            html += `<tr>
                        <td>${data[i].stage}</td>
                        <td>${data[i].fare}</td>
                     </tr>`;
        }

        html += `</tbody>`;
        return html;
    }

    // Render tables for each bus type and each range
    function renderAllTables() {
        ['ordinary', 'express', 'deluxe', 'ac', 'night'].forEach(busType => {
            for(let chunk = 0; chunk < 3; chunk++) {
                const start = chunk * 10;
                const end = start + 10;
                const tableId = `${busType}Table${chunk+1}`;
                document.getElementById(tableId).innerHTML = createTableHtml(fareData[busType], start, end);
            }
        });
    }

    renderAllTables();
	
	window.onload = function() {
      const tabcontent = document.querySelectorAll(".tabcontent");
      tabcontent.forEach((tc, index) => {
        tc.style.display = index === 0 ? "flex" : "none";
      });
    };

    // Tab switching logic
    function openTab(evt, busType) {
        const tabcontent = document.querySelectorAll(".tabcontent");
        tabcontent.forEach(tc => tc.style.display = "none");

        const tablinks = document.querySelectorAll(".tablinks");
        tablinks.forEach(btn => btn.classList.remove("active"));

        document.getElementById(busType).style.display = "flex";
        evt.currentTarget.classList.add("active");
    }
</script>

</body>
</html>

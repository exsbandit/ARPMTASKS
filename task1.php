<?php

// Upload the cumulative_sums.csv file created by the code below to Google Sheets.
// Select the data and choose "Insert" from the menu, then select "Chart".
// Choose the line chart type.
// Label the axes as "Week" and "Cumulative sum".
// Share the chart.

// Generate random data for 10 individuals and 52 weeks
$rows = 10;
$columns = 52;
$data = [];

// Generating random data
for ($i = 0; $i < $rows; $i++) {
    $data[$i] = [];
    for ($j = 0; $j < $columns; $j++) {
        $data[$i][$j] = mt_rand() / mt_getrandmax(); // Random number between 0 and 1
    }
}

// Calculating cumulative sums
$cumulativeSums = [];
for ($i = 0; $i < $rows; $i++) {
    $cumulativeSums[$i] = [];
    $sum = 0;
    for ($j = 0; $j < $columns; $j++) {
        $sum += $data[$i][$j];
        $cumulativeSums[$i][$j] = $sum;
    }
}

// Creating a CSV file to export the data to Google Sheets
$csvFile = fopen('cumulative_sums.csv', 'w');
fputcsv($csvFile, array_merge(['Week'], range(1, $rows))); // Headers
for ($j = 0; $j < $columns; $j++) {
    $row = [$j + 1]; // Weeks
    for ($i = 0; $i < $rows; $i++) {
        $row[] = $cumulativeSums[$i][$j];
    }
    fputcsv($csvFile, $row);
}
fclose($csvFile);

echo "Cumulative sums CSV file created.";
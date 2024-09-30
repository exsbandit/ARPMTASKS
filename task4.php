<?php

$employees = [
    ['name' => 'John', 'city' => 'Dallas'],
    ['name' => 'Jane', 'city' => 'Austin'],
    ['name' => 'Jake', 'city' => 'Dallas'],
    ['name' => 'Jill', 'city' => 'Dallas'],
];

$offices = [
    ['office' => 'Dallas HQ', 'city' => 'Dallas'],
    ['office' => 'Dallas South', 'city' => 'Dallas'],
    ['office' => 'Austin Branch', 'city' => 'Austin'],
];

// Use collections to create the output
$output = collect($offices)
    ->groupBy('city')
    ->map(function ($offices, $city) use ($employees) {
        // Get employee names by city
        $employeeNames = collect($employees)
            ->where('city', $city)
            ->pluck('name');

        // Map offices to their respective employees
        return $offices->mapWithKeys(function ($office) use ($employeeNames) {
            return [$office['office'] => $employeeNames->toArray()];
        });
    })
    ->toArray();

// Output the result
print_r($output);
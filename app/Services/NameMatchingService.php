<?php

namespace App\Services;

use App\Models\Employee;

class NameMatchingService
{
    /**
     * Match a given name and NIM against the employee database.
     */
    public function matchParticipant(string $inputName, string $inputNim): ?Employee
    {
        // 1. Exact NIM match (Highest Priority)
        $employeeByNim = Employee::where('nim', $inputNim)->first();
        if ($employeeByNim) {
            return $employeeByNim;
        }

        // 2. Fuzzy Name Matching among all employees
        $allEmployees = Employee::all();
        $bestMatch = null;
        $highestSimilarity = 0;
        $threshold = 85; // % similarity

        $normalizedInput = $this->normalize($inputName);

        foreach ($allEmployees as $employee) {
            $normalizedTarget = $this->normalize($employee->name);

            // Calculate similarity percentage
            similar_text($normalizedInput, $normalizedTarget, $percent);

            if ($percent > $highestSimilarity && $percent >= $threshold) {
                $highestSimilarity = $percent;
                $bestMatch = $employee;
            }
        }

        return $bestMatch;
    }

    /**
     * Normalize string for comparison.
     */
    private function normalize(string $str): string
    {
        $str = strtolower($str);
        $str = preg_replace('/[^a-z0-9]/', '', $str);

        return trim($str);
    }
}

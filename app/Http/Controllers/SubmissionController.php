<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SubmissionController extends Controller
{
    public function storeData(Request $request): \Illuminate\Http\JsonResponse
    {
        Log::info('Rozpoczynam walidację danych');

        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
                'phone' => 'required|regex:/^\d{3}-\d{3}-\d{3}$/',
                'email' => 'required|email:rfc,dns|max:100',
                'receipt_number' => 'required|string|max:50',
                'purchase_date' => 'required|date_format:d-m-Y|before_or_equal:today|after_or_equal:15-06-2024',
                'receipt_image' => 'required|file|mimes:jpeg,png,jpg,gif|max:5120',
                'accept_terms' => 'required|boolean',
                'accept_marketing' => 'nullable|boolean',
            ]);
            Log::info('Dane przeszły walidację', ['validated_data' => $validated]);
        } catch (ValidationException $e) {
            Log::error('Walidacja nie powiodła się', ['errors' => $e->errors()]);
            return response()->json($e->errors(), 422);
        }

        // Konwersja daty na format MySQL (YYYY-MM-DD)
        $validated['purchase_date'] = Carbon::createFromFormat('d-m-Y', $validated['purchase_date'])->format('Y-m-d');

        // Zapis pliku obrazu
        if ($request->hasFile('receipt_image')) {
            $path = $request->file('receipt_image')->store('receipts');
            $validated['receipt_image'] = $path;
            Log::info('Obraz został zapisany', ['image_path' => $path]);
        } else {
            Log::error('Brak obrazu w zgłoszeniu');
        }

        // Próba zapisu do bazy
        try {
            $submission = Submission::create($validated);
            Log::info('Zgłoszenie zostało zapisane w bazie', ['submission' => $submission]);
        } catch (\Exception $e) {
            Log::error('Wystąpił błąd podczas zapisu do bazy danych', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Błąd podczas zapisu do bazy danych'], 500);
        }

        return response()->json(['message' => 'Zgłoszenie zostało przyjęte!'], 201);
    }


}

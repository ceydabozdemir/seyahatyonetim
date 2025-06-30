<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // Gider detaylarını göstermek için bir metod ekleyelim
    public function showExpense($expenseId)
    {
        // Veritabanından gideri çekiyoruz
        $expense = Expense::find($expenseId);

        // Eğer gider bulunmazsa, 404 hata sayfasına yönlendirebiliriz
        if (!$expense) {
            return redirect()->route('expenses.index')->with('error', 'Gider bulunamadı.');
        }

        // Gideri view'a gönderiyoruz
        return view('expense.show', ['expense' => $expense]);
    }
}

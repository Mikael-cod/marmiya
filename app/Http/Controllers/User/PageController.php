<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PageController extends Controller
{
    public function assets(): View
    {
        return $this->page('assets', 'nav_assets', 'assets_description');
    }

    public function expense(): View
    {
        return $this->page('expense', 'nav_expense', 'expense_description');
    }

    public function recommendations(): View
    {
        return $this->page('recommendations', 'nav_recommendations', 'recommendations_description');
    }

    public function prisoners(): View
    {
        return $this->page('prisoners', 'nav_prisoners', 'prisoners_description');
    }

    public function reports(): View
    {
        return $this->page('reports', 'nav_reports', 'reports_description');
    }

    public function report(string $report): View
    {
        $reports = config('reports');

        if (! isset($reports[$report])) {
            abort(404);
        }

        $meta = $reports[$report];

        return view('user.pages.report', [
            'title' => __($meta['label']),
            'description' => __($meta['description']),
        ]);
    }

    private function page(string $view, string $titleKey, string $descriptionKey): View
    {
        return view("user.pages.{$view}", [
            'title' => __("app.user.{$titleKey}"),
            'description' => __("app.user.{$descriptionKey}"),
        ]);
    }
}

<?php

use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\BackendController;
use App\Http\Controllers\Admin\DatabaseBackupController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FrontPageController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\User\AssetRegistrationController;
use App\Http\Controllers\User\ChildrenWithMotherReportController;
use App\Http\Controllers\User\CrimeTypeReportController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\DeathSentencedReportController;
use App\Http\Controllers\User\EducationAgeReportController;
use App\Http\Controllers\User\NewIntakeReportController;
use App\Http\Controllers\User\ParoleReleasedReportController;
use App\Http\Controllers\User\RecidivistReportController;
use App\Http\Controllers\User\ReleasedReportController;
use App\Http\Controllers\User\SettingsController;
use App\Http\Controllers\User\SentenceTypeReportController;
use App\Http\Controllers\User\Under18ReportController;
use App\Http\Controllers\User\ExpenseRegistrationController;
use App\Http\Controllers\User\IncomeRegistrationController;
use App\Http\Controllers\User\PrisonerFileController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\PageController as UserPageController;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest', 'frontend.accessible'])->group(function () {
    Route::get('/', [LoginController::class, 'create']);
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/users', [AdminUserController::class, 'index'])->name('users');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        Route::get('/database', [DatabaseBackupController::class, 'index'])->name('database');
        Route::post('/database/backups', [DatabaseBackupController::class, 'store'])->name('database.store');
        Route::get('/database/backups/{backup}/download', [DatabaseBackupController::class, 'download'])
            ->where('backup', '[A-Za-z0-9_-]+\.(sql|sqlite)')
            ->name('database.download');
        Route::post('/database/backups/{backup}/restore', [DatabaseBackupController::class, 'restore'])
            ->where('backup', '[A-Za-z0-9_-]+\.(sql|sqlite)')
            ->name('database.restore');
        Route::delete('/database/backups/{backup}', [DatabaseBackupController::class, 'destroy'])
            ->where('backup', '[A-Za-z0-9_-]+\.(sql|sqlite)')
            ->name('database.destroy');
        Route::get('/front-pages', [FrontPageController::class, 'edit'])->name('front-pages');
        Route::put('/front-pages', [FrontPageController::class, 'update'])->name('front-pages.update');
        Route::get('/backend', [BackendController::class, 'index'])->name('backend');
        Route::post('/backend/actions', [BackendController::class, 'runAction'])->name('backend.actions');
        Route::get('/security', [SecurityController::class, 'edit'])->name('security');
        Route::put('/security', [SecurityController::class, 'update'])->name('security.update');
        Route::get('/activity', [ActivityController::class, 'index'])->name('activity');
        Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile');
        Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password');
    });

    Route::middleware(['role:user', 'frontend.accessible'])->name('user.')->group(function () {
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
        Route::get('/income', [IncomeRegistrationController::class, 'index'])->name('income');
        Route::get('/income/parole-release-date', [IncomeRegistrationController::class, 'paroleReleaseDate'])->name('income.parole-release-date');
        Route::post('/income', [IncomeRegistrationController::class, 'store'])->name('income.store');
        Route::put('/income/{registration}', [IncomeRegistrationController::class, 'update'])->name('income.update');
        Route::delete('/income/{registration}', [IncomeRegistrationController::class, 'destroy'])->name('income.destroy');
        Route::get('/assets', [AssetRegistrationController::class, 'index'])->name('assets');
        Route::post('/assets', [AssetRegistrationController::class, 'store'])->name('assets.store');
        Route::put('/assets/{inmate_property_registration}', [AssetRegistrationController::class, 'update'])->name('assets.update');
        Route::delete('/assets/{inmate_property_registration}', [AssetRegistrationController::class, 'destroy'])->name('assets.destroy');
        Route::get('/expense', [ExpenseRegistrationController::class, 'index'])->name('expense');
        Route::get('/expense/inmate/{registration}', [ExpenseRegistrationController::class, 'inmateData'])->name('expense.inmate');
        Route::get('/expense/{inmate_expense_registration}/export', [ExpenseRegistrationController::class, 'export'])->name('expense.export');
        Route::post('/expense', [ExpenseRegistrationController::class, 'store'])->name('expense.store');
        Route::put('/expense/{inmate_expense_registration}', [ExpenseRegistrationController::class, 'update'])->name('expense.update');
        Route::delete('/expense/{inmate_expense_registration}', [ExpenseRegistrationController::class, 'destroy'])->name('expense.destroy');
        Route::get('/recommendations', [UserPageController::class, 'recommendations'])->name('recommendations');
        Route::get('/prisoners', [PrisonerFileController::class, 'index'])->name('prisoners');
        Route::post('/prisoners', [PrisonerFileController::class, 'store'])->name('prisoners.store');
        Route::put('/prisoners/{inmate_file_record}', [PrisonerFileController::class, 'update'])->name('prisoners.update');
        Route::delete('/prisoners/{inmate_file_record}', [PrisonerFileController::class, 'destroy'])->name('prisoners.destroy');
        Route::post('/prisoners/{inmate_file_record}/pages', [PrisonerFileController::class, 'storePages'])->name('prisoners.pages.store');
        Route::delete('/prisoners/{inmate_file_record}/pages/{page}', [PrisonerFileController::class, 'destroyPage'])->name('prisoners.pages.destroy');
        Route::get('/prisoners/{inmate_file_record}/documents/export', [PrisonerFileController::class, 'exportDocuments'])->name('prisoners.documents.export');
        Route::get('/reports', [UserPageController::class, 'reports'])->name('reports');
        Route::get('/reports/crime-type/export', [CrimeTypeReportController::class, 'export'])->name('reports.crime-type.export');
        Route::get('/reports/crime-type', [CrimeTypeReportController::class, 'index'])->name('reports.crime-type');
        Route::get('/reports/education-age/export', [EducationAgeReportController::class, 'export'])->name('reports.education-age.export');
        Route::get('/reports/education-age', [EducationAgeReportController::class, 'index'])->name('reports.education-age');
        Route::get('/reports/sentence-type/export', [SentenceTypeReportController::class, 'export'])->name('reports.sentence-type.export');
        Route::get('/reports/sentence-type', [SentenceTypeReportController::class, 'index'])->name('reports.sentence-type');
        Route::get('/reports/new-intake/export', [NewIntakeReportController::class, 'export'])->name('reports.new-intake.export');
        Route::get('/reports/new-intake', [NewIntakeReportController::class, 'index'])->name('reports.new-intake');
        Route::get('/reports/released/export', [ReleasedReportController::class, 'export'])->name('reports.released.export');
        Route::get('/reports/released', [ReleasedReportController::class, 'index'])->name('reports.released');
        Route::get('/reports/under-18/export', [Under18ReportController::class, 'export'])->name('reports.under-18.export');
        Route::get('/reports/under-18', [Under18ReportController::class, 'index'])->name('reports.under-18');
        Route::get('/reports/parole-released/export', [ParoleReleasedReportController::class, 'export'])->name('reports.parole-released.export');
        Route::get('/reports/parole-released', [ParoleReleasedReportController::class, 'index'])->name('reports.parole-released');
        Route::get('/reports/children-with-mother/export', [ChildrenWithMotherReportController::class, 'export'])->name('reports.children-with-mother.export');
        Route::get('/reports/children-with-mother', [ChildrenWithMotherReportController::class, 'index'])->name('reports.children-with-mother');
        Route::get('/reports/death-sentenced/export', [DeathSentencedReportController::class, 'export'])->name('reports.death-sentenced.export');
        Route::get('/reports/death-sentenced', [DeathSentencedReportController::class, 'index'])->name('reports.death-sentenced');
        Route::get('/reports/recidivist/export', [RecidivistReportController::class, 'export'])->name('reports.recidivist.export');
        Route::get('/reports/recidivist', [RecidivistReportController::class, 'index'])->name('reports.recidivist');
        Route::get('/reports/{report}', [UserPageController::class, 'report'])
            ->whereIn('report', array_keys(config('reports')))
            ->name('reports.show');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::get('/settings', [SettingsController::class, 'edit'])->name('settings');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    });
});

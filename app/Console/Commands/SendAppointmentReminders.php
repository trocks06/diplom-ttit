<?php

namespace App\Console\Commands;

use App\Mail\AppointmentReminder;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendAppointmentReminders extends Command
{
    protected $signature = 'app:send-appointment-reminders';
    protected $description = 'Отправляет пациентам напоминания о приёме за 1 день';

    public function handle()
    {
        $tomorrow = Carbon::tomorrow()->toDateString();
        $appointments = Appointment::whereHas('schedule', function ($query) use ($tomorrow) {
            $query->whereDate('start_time', $tomorrow);
        })
            ->whereHas('patient.user')
            ->with(['patient.user', 'schedule'])
            ->get();
        $count = 0;
        foreach ($appointments as $appointment) {
            $email = $appointment->patient->user->email;
            if ($email) {
                Mail::to($email)->send(new AppointmentReminder($appointment));
                $count++;
            }
        }
        $this->info("Отправлено {$count} напоминаний о приёме на завтра ($tomorrow).");
        return Command::SUCCESS;
    }
}

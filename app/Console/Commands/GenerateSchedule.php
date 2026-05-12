<?php

namespace App\Console\Commands;

use App\Models\Doctor;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateSchedule extends Command
{
    protected $signature = 'schedule:generate
                            {doctor_id : ID врача}
                            {start_date : Дата начала (ГГГГ-ММ-ДД)}
                            {--days=1,2,3,4,5 : Дни недели (1=пн, 2=вт, ... 7=вс)}
                            {--start-time=09:00 : Начало рабочего дня}
                            {--end-time=18:00 : Конец рабочего дня}
                            {--slot-duration=30 : Длительность приёма в минутах}
                            {--breaks= : Перерывы в формате "13:00-14:00,15:30-16:00"}
                            {--weeks=1 : На сколько недель}';
    protected $description = 'Генерация слотов расписания врача';

    public function handle()
    {
        $doctorId = $this->argument('doctor_id');
        $startDate = Carbon::parse($this->argument('start_date'))->startOfDay();
        $days = explode(',', $this->option('days'));
        $startTime = $this->option('start-time');
        $endTime = $this->option('end-time');
        $slotDuration = (int) $this->option('slot-duration');
        $breaksRaw = $this->option('breaks');
        $weeks = (int) $this->option('weeks');
        $doctor = Doctor::findOrFail($doctorId);
        $breaks = [];
        if ($breaksRaw) {
            $breakPairs = explode(',', $breaksRaw);
            foreach ($breakPairs as $pair) {
                $times = explode('-', trim($pair));
                if (count($times) == 2) {
                    $breaks[] = ['start' => $times[0], 'end' => $times[1]];
                }
            }
        }

        $created = 0;
        $endDate = $startDate->copy()->addWeeks($weeks);
        for ($date = $startDate; $date < $endDate; $date = $date->copy()->addDay()) {
            $dayOfWeek = $date->dayOfWeekIso;
            if (!in_array($dayOfWeek, $days)) continue;
            $dayStart = Carbon::parse($date->format('Y-m-d') . ' ' . $startTime);
            $dayEnd = Carbon::parse($date->format('Y-m-d') . ' ' . $endTime);
            $current = $dayStart->copy();
            while ($current->copy()->addMinutes($slotDuration) <= $dayEnd) {
                $slotEnd = $current->copy()->addMinutes($slotDuration);
                $skip = false;
                foreach ($breaks as $break) {
                    $breakStart = Carbon::parse($date->format('Y-m-d') . ' ' . $break['start']);
                    $breakEnd = Carbon::parse($date->format('Y-m-d') . ' ' . $break['end']);
                    if ($current < $breakEnd && $slotEnd > $breakStart) {
                        $current = $breakEnd;
                        $skip = true;
                        break;
                    }
                }
                if ($skip) continue;
                $exists = Schedule::where('doctor_id', $doctorId)
                    ->where('start_time', $current)
                    ->exists();
                if (!$exists) {
                    Schedule::create([
                        'doctor_id' => $doctorId,
                        'start_time' => $current,
                        'end_time' => $slotEnd,
                    ]);
                    $created++;
                }
                $current->addMinutes($slotDuration);
            }
        }
        $this->info("Сгенерировано слотов: {$created}");
        return Command::SUCCESS;
    }
}

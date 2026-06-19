<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Event;
use App\Models\EventAttendee;
use App\Notifications\EventReminderNotification;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders for upcoming events';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();

        $reminders = [3, 1, 0]; // days before

        foreach ($reminders as $daysBefore) {
            $date = $today->copy()->addDays($daysBefore);

            $events = Event::whereDate('event_date', $date)
                ->where('rsvp_required', true)
                ->with('attendees.employee')
                ->get();

            foreach ($events as $event) {
                foreach ($event->attendees as $attendance) {
                    // Only remind if pending or confirmed
                    if (in_array($attendance->response_status, ['pending', 'confirmed'])) {
                        $attendance->employee->notify(new EventReminderNotification($event, $daysBefore));
                    }
                }
            }
        }

        $this->info('Event reminders sent successfully.');
    }
}

<?php

namespace App\Observers;

use App\Models\PatrolReport;
use App\Notifications\ActivityNotification;
use App\Support\NotifiableUsers;
use Illuminate\Support\Facades\Notification;

class PatrolReportObserver
{
    /**
     * Handle the PatrolReport "created" event.
     */
    public function created(PatrolReport $patrolReport): void
    {
        $users = NotifiableUsers::withPermission('patrol-report view');

        Notification::send($users, new ActivityNotification(
            title: 'Laporan Patroli Baru',
            message: "{$patrolReport->user->name} membuat laporan patroli baru.",
            url: route('patrol-reports.index'),
            type: 'info',
        ));
    }

    /**
     * Handle the PatrolReport "updated" event.
     */
    public function updated(PatrolReport $patrolReport): void
    {
        $users = NotifiableUsers::withPermission('patrol-report view');

        Notification::send($users, new ActivityNotification(
            title: 'Laporan Patroli Diperbarui',
            message: "Laporan patroli #{$patrolReport->id} telah diperbarui.",
            url: route('patrol-reports.index'),
            type: 'warning',
        ));
    }

    /**
     * Handle the PatrolReport "deleted" event.
     */
    public function deleted(PatrolReport $patrolReport): void
    {
        $users = NotifiableUsers::withPermission('patrol-report delete');

        Notification::send($users, new ActivityNotification(
            title: 'Laporan Patroli Dihapus',
            message: "Laporan patroli #{$patrolReport->id} telah dihapus.",
            type: 'danger',
        ));
    }

    /**
     * Handle the PatrolReport "restored" event.
     */
    public function restored(PatrolReport $patrolReport): void
    {
        //
    }

    /**
     * Handle the PatrolReport "force deleted" event.
     */
    public function forceDeleted(PatrolReport $patrolReport): void
    {
        //
    }
}
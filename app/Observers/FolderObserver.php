<?php

namespace App\Observers;

use App\Models\Folder;

class FolderObserver
{
    /**
     * Handle the Folder "created" event.
     */
    public function created(Folder $folder): void
    {
        //
    }

    /**
     * Handle the Folder "updated" event.
     */
    public function updated(Folder $folder): void
    {
        //
    }

    /**
     * Handle the Folder "deleted" event.
     */
    public function deleted(Folder $folder): void
    {
        if(count($folder->files)>0)
        {
            $folder->files()->delete();
        } 
    }

    /**
     * Handle the Folder "restored" event.
     */
    public function restored(Folder $folder): void
    {
        //
    }

    /**
     * Handle the Folder "force deleted" event.
     */
    public function forceDeleted(Folder $folder): void
    {
        //
    }
}

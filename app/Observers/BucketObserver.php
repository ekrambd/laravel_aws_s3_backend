<?php

namespace App\Observers;

use App\Models\Bucket;

class BucketObserver
{
    /**
     * Handle the Bucket "created" event.
     */
    public function created(Bucket $bucket): void
    {
        //
    }

    /**
     * Handle the Bucket "updated" event.
     */
    public function updated(Bucket $bucket): void
    {
        //
    }

    /**
     * Handle the Bucket "deleted" event.
     */
    public function deleted(Bucket $bucket): void
    {
        if(count($bucket->files) > 0)
        {
            $bucket->files()->delete();
        }

        if(count($bucket->folders) > 0)
        {
            $bucket->folders()->delete();
        }
    }

    /**
     * Handle the Bucket "restored" event.
     */
    public function restored(Bucket $bucket): void
    {
        //
    }

    /**
     * Handle the Bucket "force deleted" event.
     */
    public function forceDeleted(Bucket $bucket): void
    {
        //
    }
}

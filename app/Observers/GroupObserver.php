<?php

namespace App\Observers;

use App\Models\Group;

class GroupObserver
{
    public function creating(Group $group)
    {
        $group->admin_id = 1;
    }
}

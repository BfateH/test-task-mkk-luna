<?php

namespace App\Services;

use App\Models\Activity;

class ActivityService
{
    public function changeChildrenParentIdAfterDelete($model): void
    {
        $children = $model->children;

        while ($children->count()) {
            foreach ($children as $child) {
                $child->parent_id = $model->parent_id ?? null;
                $child->save();
            }
        }
    }

    public function checkDepthBeforeSaving($model): void
    {
        $changes = $model->getDirty();

        if (array_key_exists('parent_id', $changes)) {
            $newParentId = $changes['parent_id'];
            $parent = Activity::query()->find((int) $newParentId);

            if($newParentId === null) {
                return;
            }

            if (!$parent) {
                throw new \Exception("Родительская Activity не найдена");
            }

            if($parent->depth === 3) {
                throw new \Exception('Превышена максимальная глубина вложенности');
            }
        }
    }
}

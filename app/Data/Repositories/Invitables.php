<?php

namespace App\Data\Repositories;

use App\Data\Models\Invitation;
use App\Data\Models\PersonInstitution;

class Invitables extends Repository
{
    /**
     * @var string
     */
    protected $model = PersonInstitution::class;

    protected function attachInvitations($subEventId, $personInstitutions)
    {
        $relatedSubEvents = SubEvents::findById(
            $subEventId
        )->event->subEvents->pluck('id');

        $personInstitutions['rows'] = collect($personInstitutions['rows'])
            ->map(function ($personInstitution) use ($relatedSubEvents) {
                $personInstitution['invitations'] = Invitation::where(
                    'person_institution_id',
                    $personInstitution->id
                )
                    ->whereIn('sub_event_id', $relatedSubEvents)
                    ->get()
                    ->map(function ($invitation) {
                        $invitation->order = blank(
                            $invitation->subEvent->associated_subevent_id
                        )
                            ? '0000-' . $invitation->id
                            : '0001-' . $invitation->id;

                        return $invitation;
                    })
                    ->sortBy('order')
                    ->values();

                return $personInstitution;
            })
            ->toArray();

        return $personInstitutions;
    }

    protected function filterAllColumns($query, $text)
    {
        $query->where(function ($query) use ($text) {
            $query->orWhere('institutions.name', 'ilike', "%{$text}%");
            $query->orWhere('people.name', 'ilike', "%{$text}%");
            $query->orWhere('roles.name', 'ilike', "%{$text}%");
        });

        return $query;
    }

    public function getInvitables($subEventId)
    {
        return $this->attachInvitations(
            $subEventId,
            $this->applyFilter(
                app(PersonInstitutions::class)
                    ->newQuery(null)
                    ->selectRaw(
                        '(select count("invitations"."person_institution_id") from "invitations" where "person_institutions"."id" = "invitations"."person_institution_id" and "invitations"."sub_event_id" = ' .
                            $subEventId .
                            ') as is_invited_to_sub_event'
                    )
                    ->whereRaw('"person_institutions"."is_active" = true')
            )
        );
    }

    protected function filterCheckboxes($query, array $filter)
    {
        if (isset($filter['not_invited']) && $filter['not_invited']) {
            $query->whereRaw(
                'person_institutions.id not in (select person_institution_id from invitations where sub_event_id = ' .
                    $filter['not_invited'] .
                    ')'
            );
        }
    }

    /**
     * Filter Selects
     *
     * @param $query
     * @param array $filter
     * @return mixed
     */
    protected function filterSelects($query, array $filter)
    {
        if (isset($filter['sub_event']) && !is_null($filter['sub_event'])) {
            $query->invitedToSubEvent($filter['sub_event']);
        }
        if (isset($filter['role']) && !is_null($filter['role'])) {
            $query->where('role_id', $filter['role']);
        }
        if (isset($filter['institution']) && !is_null($filter['institution'])) {
            $query->where('institution_id', $filter['institution']);
        }
        if (isset($filter['category']) && !is_null($filter['category'])) {
            $query->whereRaw(
                'person_id in (select categorizable_id from categorizables where category_id = ' .
                    $filter['category'] .
                    ')'
            );
        }
        if (isset($filter['topic']) && !is_null($filter['topic'])) {
            $query->whereRaw(
                'person_id in (select person_id from person_topics where topic_id = ' .
                    $filter['topic'] .
                    ')'
            );
        }
        return $query;
    }
}

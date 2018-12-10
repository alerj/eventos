<?php

Route::group(['prefix' => '/events'], function () {
    Route::get('/', 'Events@allHasSubEventsNotFinalized')->name('events.all');

    Route::post('/', 'Events@store')->name('events.store');

    Route::post('/{id}/send-invitations', 'Events@sendInvitations')->name(
        'events.send-invitations'
    );

    Route::post('/{id}/send-credentials', 'Events@sendCredentials')->name(
        'events.send-credentials'
    );

    Route::post('/{id}', 'Events@update')->name('events.update');

    Route::group(['prefix' => '{eventId}/sub-events'], function () {
        Route::get('/', 'SubEvents@all')->name('sub-events.all');

        Route::post('/', 'SubEvents@store')->name('sub-events.store');

        Route::post('/{id}', 'SubEvents@update')->name('sub-events.update');

        Route::post('/{id}/confirm', 'SubEvents@confirm')->name(
            'sub-events.confirm'
        );

        Route::post('/{id}/finalize', 'SubEvents@finalize')->name(
            'sub-events.finalize'
        );

        Route::post(
            '/{id}/replicate-common-info',
            'SubEvents@replicateCommonInfo'
        )->name('sub-events.replicate-common-info');

        Route::get('/{id}/download', 'SubEvents@download')->name(
            'sub-events.download'
        );

        Route::group(['prefix' => '{subEventId}/receptive'], function () {
            Route::get('/', 'Receptive@invitationAccepted')->name(
                'receptive.invitationAccepted'
            );

            Route::post(
                '/{invitationId}/checkin',
                'Receptive@makeCheckin'
            )->name('receptive.makeCheckin');

            Route::post(
                '/{invitationId}/checkin-with-code',
                'Receptive@makeCheckinWithCode'
            )->name('receptive.make-checkin-with-code');
        });

        Route::group(['prefix' => '{subEventId}/invitations'], function () {
            Route::get('/', 'Invitations@all')->name('invitations.all');

            Route::get('/invitables', 'Invitations@invitables')->name(
                'invitations.invitables'
            );

            Route::post('/invitables', 'Invitations@invite')->name(
                'invitations.invite'
            );

            Route::post(
                '/invitables/move',
                'Invitations@moveInvitations'
            )->name('invitations.moveInvitations');

            Route::post('/', 'Invitations@store')->name('invitations.store');

            Route::post('/{id}', 'Invitations@update')->name(
                'invitations.update'
            );

            Route::get('/{id}/download', 'Invitations@download')->name(
                'invitations.download'
            );

            Route::get('/{id}/html', 'Invitations@html')->name(
                'invitations.html'
            );

            Route::post('/{id}/un-invite', 'Invitations@unInvite')->name(
                'invitations.un-invite'
            );

            Route::post(
                '/{id}/mark-as-accepted',
                'Invitations@markAsAccepted'
            )->name('api.invitations.mark-as-accepted');

            Route::post(
                '/{id}/mark-as-rejected',
                'Invitations@markAsRejected'
            )->name('api.invitations.mark-as-rejected');

            Route::post(
                '/{id}/mark-as-received/{type}',
                'Invitations@markAsReceived'
            )->name('api.invitations.mark-as-received');

            Route::post(
                '/{id}/send-invitation',
                'Invitations@sendInvitation'
            )->name('invitations.send-invitation');

            Route::post(
                '/{id}/send-credentials',
                'Invitations@sendCredentials'
            )->name('invitations.send-credentials');
        });
    });
});

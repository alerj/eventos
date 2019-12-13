<template>
    <div>
        <div class="py-2 text-center">
            <div class="row">
                <div class="col-12"><h2>Eventos</h2></div>
            </div>

            <div
                v-if="selected.id && events.data.links"
                class="row bg-primary text-white"
            >
                <div class="col-12 mt-2">
                    <h3>
                        {{ selected.name }}

                        <span
                            v-if="
                                subEvents.selected &&
                                    subEvents.selected.id ==
                                        events.selected.id &&
                                    subEvents.selected.name
                            "
                        >
                            - {{ subEvents.selected.name }}
                        </span>
                    </h3>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-lg-4">
                <app-table-panel
                    :title="'Eventos (' + pagination.total + ')'"
                    :add-button="{
                        uri: '/events/create',
                        disabled: cannot('events:modify'),
                        dusk: 'create-event-button',
                    }"
                    :per-page="eventsPerPage"
                    @set-per-page="eventsPerPage = $event"
                    :filter-text="eventsFilterText"
                    @input-filter-text="eventsFilterText = $event.target.value"
                >
                    <app-table
                        :pagination="events.data.links.pagination"
                        @goto-page="eventsGotoPage($event)"
                        :columns="['Nome', '']"
                    >
                        <tr
                            @click="selectEvent(event)"
                            v-for="event in events.data.rows"
                            :class="{
                                'cursor-pointer': true,
                                'bg-primary-lighter text-white': isCurrent(
                                    event,
                                    selected,
                                ),
                            }"
                        >
                            <td
                                class="align-middle text-left"
                                dusk="click-event"
                            >
                                {{ event.name }}
                            </td>

                            <td class="align-middle text-right">
                                <router-link
                                    :to="'receptive/' + event.id"
                                    tag="div"
                                    class="btn btn-primary btn-sm ml-1 pull-right"
                                    title="Receptivo"
                                >
                                    <i class="fas fa-user-friends"></i>
                                </router-link>

                                <button
                                    class="btn btn-info btn-sm text-white btn-table-utility ml-1 pull-right"
                                    @click="sendCredentials(event)"
                                    :disabled="cannot('subevents:invite')"
                                    title="Enviar todas as credenciais não enviadas"
                                >
                                    <i
                                        v-if="!event.busy"
                                        class="fa fa-landmark"
                                    ></i>
                                    <i
                                        v-if="event.busy"
                                        class="fa fa-cog fa-spin"
                                    ></i>
                                </button>

                                <button
                                    class="btn btn-warning btn-sm btn-table-utility ml-1 pull-right"
                                    @click="sendInvitations(event)"
                                    :disabled="cannot('subevents:invite')"
                                    title="Enviar todos os convites não enviados"
                                >
                                    <i
                                        v-if="!event.busy"
                                        class="fa fa-angle-double-right"
                                    ></i>
                                    <i
                                        v-if="event.busy"
                                        class="fa fa-cog fa-spin"
                                    ></i>
                                </button>

                                <router-link
                                    :to="'/events/' + event.id + '/update'"
                                    tag="button"
                                    class="btn btn-danger btn-sm btn-table-utility ml-1 pull-right"
                                    :disabled="cannot('events:modify')"
                                    title="Editar Evento"
                                >
                                    <i class="fa fa-edit"></i>
                                </router-link>
                            </td>
                        </tr>
                    </app-table>
                </app-table-panel>
            </div>

            <div class="col-12 col-lg-8">
                <app-table-panel
                    v-if="
                        (can('subevents:read') || can('subevents:modify')) &&
                            selected.id &&
                            subEvents.data.links
                    "
                    :title="
                        selected.name +
                            ' (' +
                            subEvents.data.links.pagination.total +
                            ' subeventos)'
                    "
                    :add-button="{
                        uri:
                            '/events/' +
                            subEvents.event.id +
                            '/sub-events/create',
                        disabled: cannot('subevents:modify'),
                        dusk: 'create-sub-event',
                    }"
                    :per-page="subEventsPerPage"
                    @set-per-page="subEventsPerPage = $event"
                    :filter-text="subEventsFilterText"
                    @input-filter-text="
                        subEventsFilterText = $event.target.value
                    "
                >
                    <app-table
                        :pagination="subEvents.data.links.pagination"
                        @goto-page="subEventsGotoPage($event)"
                        :columns="[
                            'Setor',
                            'Nome',
                            {
                                type: 'label',
                                title: 'Convidados',
                                trClass: 'text-right',
                            },
                            'Data',
                            'Hora',
                            'Confirmado',
                            'Realizado',
                            '',
                        ]"
                    >
                        <tr
                            @click="selectSubEvent(subEvent)"
                            v-for="subEvent in subEvents.data.rows"
                            class="cursor-pointer"
                            :class="{
                                'cursor-pointer': true,
                                'bg-primary-lighter text-white': isCurrent(
                                    subEvent,
                                    subEvents.selected,
                                ),
                            }"
                        >
                            <td class="align-middle">
                                <app-sector-badge
                                    :sector="subEvent.sector"
                                    uppercase="true"
                                    padding="2"
                                ></app-sector-badge>
                            </td>

                            <td class="align-middle">
                                <strong>{{ subEvent.name }}</strong
                                ><br />
                                {{ subEvent.place }}
                            </td>

                            <td class="align-middle text-right">
                                {{ subEvent.invitations_count }}
                            </td>

                            <td class="align-middle" dusk="sub-event">
                                {{ subEvent.date }}
                            </td>

                            <td class="align-middle">{{ subEvent.time }}</td>

                            <td class="align-middle">
                                {{ subEvent.confirmed_at }}
                            </td>

                            <td class="align-middle">
                                {{ subEvent.ended_at }}
                            </td>

                            <td
                                class="align-middle text-right subevents-buttons"
                            >
                                <button
                                    v-if="!subEvent.associated_subevent_id"
                                    class="btn btn-info btn-sm btn-table-utility text-white ml-1 pull-right"
                                    @click="replicateCommonInfo(subEvent)"
                                    :disabled="cannot('subevents:modify')"
                                    title="Replicar textos para todos os outros subeventos"
                                >
                                    <i class="fa fa-copy"></i>
                                </button>

                                <button
                                    v-if="!subEvent.confirmed_at"
                                    class="btn btn-success btn-sm btn-table-utility ml-1 pull-right"
                                    dusk="confirm-subevent"
                                    @click="confirmSubEvent(subEvent)"
                                    :disabled="
                                        cannot('subevents:modify') ||
                                            !environment.events.confirmation
                                                .enabled
                                    "
                                    title="Confirmar Sub-evento"
                                >
                                    <i class="fa fa-check"></i>
                                </button>

                                <button
                                    v-if="
                                        !subEvent.ended_at &&
                                            subEvent.confirmed_at
                                    "
                                    class="btn btn-primary btn-sm btn-table-utility ml-1 pull-right"
                                    @click="finalizeSubEvent(subEvent)"
                                    :disabled="cannot('subevents:modify')"
                                    title="Finalizar Sub-evento"
                                >
                                    <i class="fa fa-times-circle"></i>
                                </button>

                                <router-link
                                    :to="
                                        'events/' +
                                            subEvents.event.id +
                                            '/sub-events/' +
                                            subEvent.id +
                                            '/update'
                                    "
                                    tag="button"
                                    class="btn btn-danger btn-sm btn-table-utility ml-1 pull-right"
                                    :disabled="cannot('subevents:modify')"
                                    title="Editar Sub-evento"
                                >
                                    <i class="fa fa-edit"></i>
                                </router-link>

                                <button
                                    class="btn btn-warning btn-sm btn-table-utility ml-1 pull-right"
                                    @click="downloadSubEventPDF(subEvent)"
                                    title="Imprimir lista de convidados"
                                >
                                    <i
                                        v-if="!downloading"
                                        class="fa fa-print"
                                    ></i>
                                    <i
                                        v-if="downloading"
                                        class="fa fa-cog fa-spin"
                                    ></i>
                                </button>
                            </td>
                        </tr>
                    </app-table>
                </app-table-panel>
            </div>
        </div>

        <div class="row" v-if="invitations.subEvent.id">
            <div class="col-12">
                <app-table-panel
                    v-if="
                        (can('subevents:read') || can('subevents:modify')) &&
                            selected.id &&
                            invitations.data.links
                    "
                    :title="
                        invitations.data.links.pagination.total +
                            ' convidado' +
                            (invitations.data.links.pagination.total == 1
                                ? ''
                                : 's') +
                            ' para ' +
                            subEvents.selected.name +
                            ' de ' +
                            selected.name
                    "
                    :add-button="{
                        uri:
                            '/events/' +
                            subEvents.event.id +
                            '/sub-events/' +
                            subEvents.selected.id +
                            '/invitations/create',
                        disabled: cannot('subevents:invite'),
                        dusk: 'add-guest',
                    }"
                    :per-page="invitationsPerPage"
                    @set-per-page="invitationsPerPage = $event"
                    :filter-text="invitationsFilterText"
                    @input-filter-text="
                        invitationsFilterText = $event.target.value
                    "
                    dont-center-filters="true"
                >
                    <template slot="checkboxes">
                        <div class="row">
                            <div class="col">
                                <app-input
                                    name="hasNoEmailCheckbox"
                                    label="sem e-mail"
                                    type="checkbox"
                                    v-model="hasNoEmailCheckbox"
                                    :required="true"
                                    :form="form"
                                    inline="true"
                                ></app-input>
                            </div>

                            <div class="col">
                                <app-input
                                    name="sentCheckbox"
                                    label="enviados"
                                    type="checkbox"
                                    v-model="sentCheckbox"
                                    :required="true"
                                    :form="form"
                                    inline="true"
                                ></app-input>

                                <br />

                                <app-input
                                    name="notSentCheckbox"
                                    label="não enviados"
                                    type="checkbox"
                                    v-model="notSentCheckbox"
                                    :required="true"
                                    :form="form"
                                    inline="true"
                                ></app-input>
                            </div>

                            <div class="col">
                                <app-input
                                    name="receivedCheckbox"
                                    label="recebidos"
                                    type="checkbox"
                                    v-model="receivedCheckbox"
                                    :required="true"
                                    :form="form"
                                    inline="true"
                                ></app-input>

                                <br />

                                <app-input
                                    name="notReceivedCheckbox"
                                    label="não recebidos"
                                    type="checkbox"
                                    v-model="notReceivedCheckbox"
                                    :required="true"
                                    :form="form"
                                    inline="true"
                                ></app-input>
                            </div>

                            <div class="col">
                                <app-input
                                    name="acceptedCheckbox"
                                    label="aceitos"
                                    type="checkbox"
                                    v-model="acceptedCheckbox"
                                    :required="true"
                                    :form="form"
                                    inline="true"
                                ></app-input>

                                <br />

                                <app-input
                                    name="notAcceptedCheckbox"
                                    label="não aceitos"
                                    type="checkbox"
                                    v-model="notAcceptedCheckbox"
                                    :required="true"
                                    :form="form"
                                    inline="true"
                                ></app-input>

                                <app-input
                                    name="declinedCheckbox"
                                    label="declinados"
                                    type="checkbox"
                                    v-model="declinedCheckbox"
                                    :required="true"
                                    :form="form"
                                    inline="true"
                                ></app-input>
                            </div>

                            <div class="col">
                                <app-input
                                    name="credentialsSentCheckbox"
                                    label="credenciais enviadas"
                                    type="checkbox"
                                    v-model="credentialsSentCheckbox"
                                    :required="true"
                                    :form="form"
                                    inline="true"
                                ></app-input>

                                <br />

                                <app-input
                                    name="credentialsNotSentCheckbox"
                                    label="credenciais não enviadas"
                                    type="checkbox"
                                    v-model="credentialsNotSentCheckbox"
                                    :required="true"
                                    :form="form"
                                    inline="true"
                                ></app-input>
                            </div>

                            <div class="col">
                                <app-input
                                    name="credentialsReceivedCheckbox"
                                    label="credenciais recebidas"
                                    type="checkbox"
                                    v-model="credentialsReceivedCheckbox"
                                    :required="true"
                                    :form="form"
                                    inline="true"
                                ></app-input>

                                <br />

                                <app-input
                                    name="credentialsNotReceivedCheckbox"
                                    label="credenciais não recebidas"
                                    type="checkbox"
                                    v-model="credentialsNotReceivedCheckbox"
                                    :required="true"
                                    :form="form"
                                    inline="true"
                                ></app-input>
                            </div>

                            <div class="col">
                                <app-input
                                    name="notCheckedInCheckbox"
                                    label="não fizeram check in"
                                    type="checkbox"
                                    v-model="notCheckedInCheckbox"
                                    :required="true"
                                    :form="form"
                                    inline="true"
                                ></app-input>
                            </div>
                        </div>
                    </template>

                    <app-table
                        :pagination="invitations.data.links.pagination"
                        @goto-page="invitationsGotoPage($event)"
                        :columns="[
                            'Código',
                            'Convidado',
                            {
                                type: 'label',
                                title: 'Pendências',
                                trClass: 'text-center',
                            },
                            {
                                type: 'label',
                                title: 'Convite',
                                trClass: 'text-center',
                            },
                            {
                                type: 'label',
                                title: 'Credencial',
                                trClass: 'text-center',
                            },
                            {
                                type: 'label',
                                title: 'Check in',
                                trClass: 'text-center',
                            },
                            '',
                        ]"
                    >
                        <tr
                            @click="selectInvitation(invitation)"
                            v-for="invitation in invitations.data.rows"
                            :class="{
                                'cursor-pointer': true,
                                'bg-primary-lighter text-white': isCurrent(
                                    invitation,
                                    invitations.selected,
                                ),
                            }"
                        >
                            <td class="align-middle">{{ invitation.code }}</td>
                            <!-- Código -->

                            <td class="align-middle">
                                <!-- Convidado -->
                                <strong>{{
                                    invitation.person_institution.person.name
                                }}</strong>

                                ({{ invitation.person_institution.title }})

                                <br />

                                {{ invitation.person_institution.role.name }} -
                                {{
                                    invitation.person_institution.institution
                                        .name
                                }}

                                <router-link
                                    :to="
                                        '/events/' +
                                            invitation.sub_event.event.id +
                                            '/sub-events/' +
                                            invitation.sub_event.id +
                                            '/invitations/' +
                                            invitation.id +
                                            '/change'
                                    "
                                    tag="button"
                                    class="badge bg-info text-white cursor-pointer"
                                    :disabled="
                                        cannot('subevents:invite') ||
                                            cannot('people:modify')
                                    "
                                    title="modificar instituição"
                                >
                                    <i class="fa fa-edit"></i>
                                </router-link>
                            </td>

                            <td class="align-middle text-center">
                                <!-- Pendências -->
                                <h6
                                    v-for="pending in invitation.pending"
                                    class="m-0"
                                >
                                    <span
                                        :class="'badge badge-' + pending.type"
                                        >{{ pending.label }}</span
                                    >
                                </h6>
                            </td>

                            <td class="align-middle text-center">
                                <!-- Convite enviado -->
                                <h6 class="mb-0">
                                    <span
                                        v-if="invitation.sent_at"
                                        class="badge badge-success"
                                        >enviados:
                                        {{
                                            filterNotifications(
                                                invitation.notifications,
                                                'invitation',
                                            ).length
                                        }}</span
                                    >
                                    <span v-else class="badge badge-danger"
                                        >não enviado</span
                                    >
                                </h6>

                                <h6 class="mb-0">
                                    <span
                                        v-if="
                                            invitation.received_at &&
                                                !invitation.received_by_id
                                        "
                                        class="badge badge-success"
                                        >recebidos:
                                        {{
                                            filterNotificationsReceived(
                                                invitation.notifications,
                                                'invitation',
                                            ).length
                                        }}</span
                                    >
                                    <span
                                        v-if="
                                            invitation.received_at &&
                                                invitation.received_by_id
                                        "
                                        class="badge badge-warning"
                                        >recebido manualmente</span
                                    >
                                    <span
                                        v-if="!invitation.received_at"
                                        class="badge badge-danger"
                                        >não recebido</span
                                    >
                                </h6>

                                <h6 class="mb-0">
                                    <span
                                        v-if="
                                            invitation.accepted_at &&
                                                invitation.accepted_by_id
                                        "
                                        class="badge badge-warning"
                                        >aceito manualmente</span
                                    >
                                    <span
                                        v-if="
                                            invitation.declined_at &&
                                                invitation.declined_by_id
                                        "
                                        class="badge badge-warning"
                                        >declinado manualmente</span
                                    >

                                    <template v-if="invitation.sent_at">
                                        <span
                                            v-if="
                                                !invitation.received_at &&
                                                    !invitation.accepted_at &&
                                                    !invitation.declined_at
                                            "
                                            class="badge badge-danger"
                                            >não recebido</span
                                        >
                                        <span
                                            v-if="
                                                invitation.received_at &&
                                                    !invitation.accepted_at &&
                                                    !invitation.declined_at
                                            "
                                            class="badge badge-primary"
                                            >não respondeu</span
                                        >

                                        <span
                                            v-if="
                                                !invitation.accepted_by_id &&
                                                    invitation.accepted_at
                                            "
                                            class="badge badge-success"
                                            >aceitou</span
                                        >
                                        <span
                                            v-if="
                                                !invitation.declined_by_id &&
                                                    invitation.declined_at
                                            "
                                            class="badge badge-danger"
                                            >declinou</span
                                        >
                                    </template>
                                </h6>
                            </td>

                            <td class="align-middle text-center">
                                <!-- Credencial enviada -->
                                <span v-if="!invitation.declined_at">
                                    <h6 class="mb-0">
                                        <span
                                            v-if="
                                                invitation.credentials_sent_at
                                            "
                                            class="badge badge-success"
                                            >enviadas:
                                            {{
                                                filterNotifications(
                                                    invitation.notifications,
                                                    'credentials',
                                                ).length
                                            }}</span
                                        >
                                        <span v-else class="badge badge-danger"
                                            >não enviada</span
                                        >
                                    </h6>

                                    <h6 class="mb-0">
                                        <span
                                            v-if="
                                                invitation.credentials_received_at &&
                                                    !invitation.credentials_received_by_id
                                            "
                                            class="badge badge-success"
                                            >recebidas:
                                            {{
                                                filterNotificationsReceived(
                                                    invitation.notifications,
                                                    'credentials',
                                                ).length
                                            }}</span
                                        >
                                        <span
                                            v-if="
                                                invitation.credentials_received_at &&
                                                    invitation.credentials_received_by_id
                                            "
                                            class="badge badge-warning"
                                            >recebida manualmente</span
                                        >
                                        <span
                                            v-if="
                                                !invitation.credentials_received_at
                                            "
                                            class="badge badge-danger"
                                            >não recebida</span
                                        >
                                    </h6>
                                </span>

                                <span v-else>
                                    <h6 class="mb-0">
                                        <span class="badge badge-primary"
                                            >convite declinado</span
                                        >
                                    </h6>
                                </span>
                            </td>

                            <td class="align-middle text-center">
                                {{ invitation.checkin_at }}
                            </td>

                            <td class="align-middle text-right">
                                <button
                                    :disabled="cannot('subevents:invite')"
                                    @click="sendInvitation(invitation)"
                                    class="btn btn-info btn-sm btn-sm btn-table-utility text-white ml-1 pull-right"
                                    v-if="
                                        can('subevents:invite') &&
                                            canSendEmail(invitation) &&
                                            invitation.sub_event
                                                .send_invitations
                                    "
                                    title="Enviar convite"
                                >
                                    <i dusk="enviar-convite" class="fa fa-mail-bulk"></i>
                                </button>

                                <button
                                    :disabled="
                                        cannot('subevents:invite') ||
                                            !invitation.accepted_at
                                    "
                                    @click="sendCredential(invitation)"
                                    class="btn btn-info btn-sm btn-sm btn-table-utility text-white ml-1 pull-right"
                                    v-if="
                                        can('subevents:invite') &&
                                            canSendEmail(invitation) &&
                                            invitation.sub_event
                                                .send_credentials
                                    "
                                    title="Enviar credenciais"
                                >
                                    <i dusk="send-credentials" class="fa fa-landmark"></i>
                                </button>

                                <button
                                    :disabled="cannot('subevents:invite')"
                                    @click="
                                        markAsReceived(invitation, 'invitation')
                                    "
                                    class="btn btn-success btn-sm btn-table-utility ml-1 pull-right"
                                    v-if="
                                        can('subevents:modify') &&
                                            invitation.sub_event.confirmed_at &&
                                            !invitation.received_at
                                    "
                                    title="Marcar o convite como 'recebido manualmente'"
                                >
                                    <i class="fa fa-check"></i>
                                </button>

                                <button
                                    :disabled="cannot('subevents:invite')"
                                    @click="
                                        markAsReceived(
                                            invitation,
                                            'credentials',
                                        )
                                    "
                                    class="btn btn-warning btn-sm btn-table-utility ml-1 pull-right"
                                    v-if="
                                        can('subevents:invite') &&
                                            invitation.sub_event.confirmed_at &&
                                            invitation.accepted_at &&
                                            !invitation.credentials_received_at
                                    "
                                    title="Marcar as credenciais como 'recebida manualmente'"
                                >
                                    <i class="fa fa-check"></i>
                                </button>

                                <button
                                    :disabled="cannot('subevents:invite')"
                                    @click="markAsAccepted(invitation)"
                                    class="btn btn-success btn-sm btn-table-utility ml-1 pull-right"
                                    v-if="
                                        can('subevents:invite') &&
                                            invitation.sub_event.confirmed_at &&
                                            !invitation.accepted_at
                                    "
                                    title="Aceitar o convite manualmente"
                                >
                                    <i dusk="accept-invitation" class="fa fa-check-double"></i>
                                </button>

                                <button
                                    :disabled="cannot('subevents:invite')"
                                    @click="markAsDeclined(invitation)"
                                    class="btn btn-danger btn-sm btn-table-utility ml-1 pull-right"
                                    v-if="
                                        can('subevents:invite') &&
                                            invitation.sub_event.confirmed_at &&
                                            !invitation.declined_at
                                    "
                                    title="Declinar o convite manualmente"
                                >
                                    <i class="fa fa-calendar-times"></i>
                                </button>

                                <button
                                    @click="downloadInvitation(invitation)"
                                    class="btn btn-warning btn-sm btn-table-utility ml-1 pull-right"
                                    v-if="
                                        can('subevents:read') &&
                                            canSendEmail(invitation) &&
                                            invitation.accepted_at
                                    "
                                    title="Baixar PDF das credenciais"
                                >
                                    <i
                                        v-if="!invitation.busy"
                                        class="fa fa-id-badge"
                                    ></i>
                                    <i
                                        v-if="invitation.busy"
                                        class="fa fa-cog fa-spin"
                                    ></i>
                                </button>

                                <router-link
                                    :to="
                                        '/events/' +
                                            invitation.sub_event.event.id +
                                            '/sub-events/' +
                                            invitation.sub_event.id +
                                            '/invitations/' +
                                            invitation.id +
                                            '/show'
                                    "
                                    tag="button"
                                    class="btn btn-warning btn-sm btn-table-utility ml-1 pull-right"
                                    title="Ver todos os dados do convite"
                                >
                                    <i class="fa fa-eye"></i>
                                </router-link>

                                <button
                                    :disabled="cannot('subevents:invite')"
                                    @click="unInvite(invitation)"
                                    class="btn btn-danger btn-sm btn-table-utility ml-1 pull-right"
                                    v-if="!invitation.sent_at"
                                    title="Excluir convite (ainda não foi enviado)"
                                >
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </app-table>
                </app-table-panel>
            </div>
        </div>
    </div>
</template>

<script>
import crud from './mixins/crud'
import events from './mixins/events'
import permissions from './mixins/permissions'
import { mapActions, mapState } from 'vuex'

const service = { name: 'events', uri: 'events' }

export default {
    mixins: [crud, events, permissions],

    data() {
        return {
            service: service,
            downloading: false,
        }
    },

    methods: {
        ...mapActions(service.name, [
            'selectEvent',
            'selectSubEvent',
            'selectInvitation',
        ]),

        eventsGotoPage(page) {
            this.gotoPage(page, 'events', this.events.data.links.pagination)
        },

        subEventsGotoPage(page) {
            this.gotoPage(
                page,
                'subEvents',
                this.subEvents.data.links.pagination,
            )
        },

        invitationsGotoPage(page) {
            this.gotoPage(
                page,
                'invitations',
                this.invitations.data.links.pagination,
            )
        },

        unInvite(invitation) {
            confirm(
                'Deseja realmente desconvidar ' +
                    invitation.person_institution.person.name +
                    '?',
                this,
            ).then(value => {
                if (value) {
                    return this.$store.dispatch(
                        'invitations/unInvite',
                        invitation,
                    )
                }
            })
        },

        markAsAccepted(invitation) {
            confirm(
                'Deseja realmente confirmar a presença de ' +
                    invitation.person_institution.person.name +
                    '?',
                this,
            ).then(value => {
                if (value) {
                    return this.$store.dispatch(
                        'invitations/markAsAccepted',
                        invitation,
                    )
                }
            })
        },

        markAsReceived(invitation, type) {
            confirm(
                'Deseja realmente marcar como recebido o covite de ' +
                    invitation.person_institution.person.name +
                    '?',
                this,
            ).then(value => {
                if (value) {
                    return this.$store.dispatch('invitations/markAsReceived', {
                        invitation: invitation,
                        type: type,
                    })
                }
            })
        },

        markAsDeclined(invitation) {
            confirm(
                'Deseja realmente declinar a presença de ' +
                    invitation.person_institution.person.name +
                    '?',
                this,
            ).then(value => {
                if (value) {
                    return this.$store.dispatch(
                        'invitations/markAsDeclined',
                        invitation,
                    )
                }
            })
        },

        downloadInvitation(invitation) {
            invitation.busy = true

            downloadPDF(
                this.$store.getters['invitations/getDataUrl'] +
                    '/' +
                    invitation.id +
                    '/download',
            ).then(() => {
                invitation.busy = false
            })
        },

        sendCredential(invitation) {
            invitation.busy = true
            confirm(
                'Deseja realmente enviar as credencias para ' +
                    invitation.person_institution.person.name +
                    '?',
                this,
            ).then(value => {
                if (value) {
                    return this.$store.dispatch(
                        'invitations/sendCredentials',
                        invitation,
                    )
                }
            })
        },

        sendInvitation(invitation) {
            invitation.busy = true
            confirm(
                'Deseja realmente enviar o convite para ' +
                    invitation.person_institution.person.name +
                    '?',
                this,
            ).then(value => {
                if (value) {
                    return this.$store.dispatch(
                        'invitations/sendInvitation',
                        invitation,
                    )
                }
            })
        },

        confirmSubEvent(subEvent) {
            confirm(
                'Deseja realmente confirmar ' + subEvent.name + '?',
                this,
            ).then(value => {
                if (value) {
                    return this.$store.dispatch('subEvents/confirm', subEvent)
                }
            })
        },

        finalizeSubEvent(subEvent) {
            confirm(
                'Deseja realmente confirmar que o evento foi realizado ' +
                    subEvent.name +
                    '?',
                this,
            ).then(value => {
                if (value) {
                    this.finalizeSubEventReconfirmed(subEvent)
                }
            })
        },

        finalizeSubEventReconfirmed(subEvent) {
            confirm(
                'Tem certeza que deseja marcar este sub-evento como realizado?',
                this,
            ).then(value => {
                if (value) {
                    return this.$store.dispatch('subEvents/finalize', subEvent)
                }
            })
        },

        sendInvitations(event) {
            confirm(
                'Você tem certeza que deseja enviar todos os convites agora?',
                this,
            ).then(value => {
                if (value) {
                    event.busy = true

                    return this.$store
                        .dispatch('events/sendInvitations', event)
                        .then(() => {
                            event.busy = false
                        })
                }
            })
        },

        sendCredentials(event) {
            confirm(
                'Você tem certeza que deseja enviar todas as credenciais agora?',
                this,
            ).then(value => {
                if (value) {
                    event.busy = true

                    return this.$store
                        .dispatch('events/sendCredentials', event)
                        .then(() => {
                            event.busy = false
                        })
                }
            })
        },

        sendCredentials(event) {
            confirm(
                'Você tem certeza que deseja enviar todas as credenciais agora?',
                this,
            ).then(value => {
                if (value) {
                    event.busy = true

                    return this.$store
                        .dispatch('events/sendCredentials', event)
                        .then(() => {
                            event.busy = false
                        })
                }
            })
        },

        downloadSubEventPDF(subEvent) {
            this.downloading = true

            downloadPDF(
                this.$store.getters['subEvents/getDataUrl'] +
                    '/' +
                    subEvent.id +
                    '/download',
            ).then(() => {
                this.downloading = false
            })
        },

        canSendEmail(invitation) {
            return invitation.has_email && invitation.sub_event.confirmed_at
        },

        replicateCommonInfo(subEvent) {
            confirm(
                'Você tem certeza que replicar os textos de "' +
                    subEvent.name +
                    ' - ' +
                    subEvent.sector.name +
                    '" para os outros sub-eventos?',
                this,
            ).then(value => {
                confirm('CERTEZA ABSOLUTA?', this).then(value => {
                    if (value) {
                        subEvent.busy = true

                        return this.$store
                            .dispatch('subEvents/replicateCommonInfo', subEvent)
                            .then(() => {
                                subEvent.busy = false
                            })
                    }
                })
            })
        },

        filterNotifications(notifications, type) {
            return _.filter(notifications, notification => {
                return notification.content_type === type
            })
        },

        filterNotificationsReceived(notifications, type) {
            return _.filter(
                this.filterNotifications(notifications, type),
                notification => {
                    return notification.received_at
                },
            )
        },
    },

    computed: {
        eventsFilterText: {
            get() {
                return this.$store.state['events'].data.filter.text
            },

            set(filter) {
                return this.$store.dispatch(
                    this.service.name + '/mutateSetQueryFilterText',
                    filter,
                )
            },
        },

        eventsPerPage: {
            get() {
                return this.$store.state['events'].data.links.pagination
                    .per_page
            },

            set(perPage) {
                return this.$store.dispatch('events/setPerPage', perPage)
            },
        },

        subEventsFilterText: {
            get() {
                return this.$store.state['subEvents'].data.filter.text
            },

            set(filter) {
                return this.$store.dispatch(
                    'subEvents/mutateSetQueryFilterText',
                    filter,
                )
            },
        },

        subEventsPerPage: {
            get() {
                return this.$store.state['subEvents'].data.links.pagination
                    .per_page
            },

            set(perPage) {
                return this.$store.dispatch('subEvents/setPerPage', perPage)
            },
        },

        invitationsFilterText: {
            get() {
                return this.$store.state['invitations'].data.filter.text
            },

            set(filter) {
                return this.$store.dispatch(
                    'invitations/mutateSetQueryFilterText',
                    filter,
                )
            },
        },

        hasNoEmailCheckbox: {
            get() {
                return this.$store.state['invitations'].data.filter.checkboxes
                    .hasNoEmail
            },

            set(filter) {
                this.$store.commit('invitations/mutateFilterCheckbox', {
                    field: 'hasNoEmail',
                    value: filter,
                })

                this.$store.dispatch('invitations/load')
            },
        },

        sentCheckbox: {
            get() {
                return this.$store.state['invitations'].data.filter.checkboxes
                    .sent
            },

            set(filter) {
                this.$store.commit('invitations/mutateFilterCheckbox', {
                    field: 'sent',
                    value: filter,
                })

                this.$store.dispatch('invitations/load')
            },
        },

        notSentCheckbox: {
            get() {
                return this.$store.state['invitations'].data.filter.checkboxes
                    .notSent
            },

            set(filter) {
                this.$store.commit('invitations/mutateFilterCheckbox', {
                    field: 'notSent',
                    value: filter,
                })

                this.$store.dispatch('invitations/load')
            },
        },

        receivedCheckbox: {
            get() {
                return this.$store.state['invitations'].data.filter.checkboxes
                    .received
            },

            set(filter) {
                this.$store.commit('invitations/mutateFilterCheckbox', {
                    field: 'received',
                    value: filter,
                })

                this.$store.dispatch('invitations/load')
            },
        },

        notReceivedCheckbox: {
            get() {
                return this.$store.state['invitations'].data.filter.checkboxes
                    .notReceived
            },

            set(filter) {
                this.$store.commit('invitations/mutateFilterCheckbox', {
                    field: 'notReceived',
                    value: filter,
                })

                this.$store.dispatch('invitations/load')
            },
        },

        credentialsSentCheckbox: {
            get() {
                return this.$store.state['invitations'].data.filter.checkboxes
                    .credentialsSent
            },

            set(filter) {
                this.$store.commit('invitations/mutateFilterCheckbox', {
                    field: 'credentialsSent',
                    value: filter,
                })

                this.$store.dispatch('invitations/load')
            },
        },

        credentialsNotSentCheckbox: {
            get() {
                return this.$store.state['invitations'].data.filter.checkboxes
                    .credentialsNotSent
            },

            set(filter) {
                this.$store.commit('invitations/mutateFilterCheckbox', {
                    field: 'credentialsNotSent',
                    value: filter,
                })

                this.$store.dispatch('invitations/load')
            },
        },

        credentialsReceivedCheckbox: {
            get() {
                return this.$store.state['invitations'].data.filter.checkboxes
                    .credentialsReceived
            },

            set(filter) {
                this.$store.commit('invitations/mutateFilterCheckbox', {
                    field: 'credentialsReceived',
                    value: filter,
                })

                this.$store.dispatch('invitations/load')
            },
        },

        credentialsNotReceivedCheckbox: {
            get() {
                return this.$store.state['invitations'].data.filter.checkboxes
                    .credentialsNotReceived
            },

            set(filter) {
                this.$store.commit('invitations/mutateFilterCheckbox', {
                    field: 'credentialsNotReceived',
                    value: filter,
                })

                this.$store.dispatch('invitations/load')
            },
        },

        acceptedCheckbox: {
            get() {
                return this.$store.state['invitations'].data.filter.checkboxes
                    .accepted
            },

            set(filter) {
                this.$store.commit('invitations/mutateFilterCheckbox', {
                    field: 'accepted',
                    value: filter,
                })

                this.$store.dispatch('invitations/load')
            },
        },

        declinedCheckbox: {
            get() {
                return this.$store.state['invitations'].data.filter.checkboxes
                    .declined
            },

            set(filter) {
                this.$store.commit('invitations/mutateFilterCheckbox', {
                    field: 'declined',
                    value: filter,
                })

                this.$store.dispatch('invitations/load')
            },
        },

        notAcceptedCheckbox: {
            get() {
                return this.$store.state['invitations'].data.filter.checkboxes
                    .notAccepted
            },

            set(filter) {
                this.$store.commit('invitations/mutateFilterCheckbox', {
                    field: 'notAccepted',
                    value: filter,
                })

                this.$store.dispatch('invitations/load')
            },
        },

        notCheckedInCheckbox: {
            get() {
                return this.$store.state['invitations'].data.filter.checkboxes
                    .notCheckedIn
            },

            set(filter) {
                this.$store.commit('invitations/mutateFilterCheckbox', {
                    field: 'notCheckedIn',
                    value: filter,
                })

                this.$store.dispatch('invitations/load')
            },
        },

        notAnsweredCheckbox: {
            get() {
                return this.$store.state['invitations'].data.filter.checkboxes
                    .notAnswered
            },

            set(filter) {
                this.$store.commit('invitations/mutateFilterCheckbox', {
                    field: 'notAnswered',
                    value: filter,
                })

                this.$store.dispatch('invitations/load')
            },
        },

        invitationsPerPage: {
            get() {
                return this.$store.state['invitations'].data.links.pagination
                    .per_page
            },

            set(perPage) {
                return this.$store.dispatch('invitations/setPerPage', perPage)
            },
        },
    },
}
</script>

<style></style>

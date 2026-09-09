<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Docker Engine API
    |--------------------------------------------------------------------------
    | Pfad zum Unix-Socket (innerhalb des app-Containers) oder URL zu einem
    | Docker-Socket-Proxy. Kommt aus der .env (DOCKER_SOCKET_PATH).
    */
    'socket_path' => env('DOCKER_SOCKET_PATH', '/var/run/docker.sock'),

    /* Pfad zum gemounteten Host-/proc für Host-Metriken. */
    'host_proc_path' => env('HOST_PROC_PATH', '/host/proc'),
];

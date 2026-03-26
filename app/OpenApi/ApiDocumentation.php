<?php

namespace App\OpenApi;

/**
 * @OA\Info(
 *     title="ETHOS Backend API",
 *     version="1.0.0",
 *     description="Documentación integral del backend de ETHOS. Incluye endpoints de autenticación, administración, CRM (clientes/proyectos), perfil de usuario y utilidades de API."
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Servidor principal de ETHOS"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sessionAuth",
 *     type="apiKey",
 *     in="cookie",
 *     name="laravel_session",
 *     description="Autenticación por sesión web de Laravel. Para métodos mutables (POST/PUT/PATCH/DELETE) envía también X-CSRF-TOKEN."
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="csrfHeader",
 *     type="apiKey",
 *     in="header",
 *     name="X-CSRF-TOKEN",
 *     description="Token CSRF requerido para peticiones mutables de rutas web protegidas."
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctumBearer",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="token",
 *     description="Bearer token para rutas API con auth:sanctum."
 * )
 *
 * @OA\Schema(
 *     schema="MessageResponse",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="Operación completada exitosamente")
 * )
 *
 * @OA\Schema(
 *     schema="ValidationErrorResponse",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="The given data was invalid."),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         additionalProperties=@OA\Schema(type="array", @OA\Items(type="string"))
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="ClientResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=10),
 *     @OA\Property(property="name", type="string", example="Acme Corp"),
 *     @OA\Property(property="industry", type="string", nullable=true, example="Manufactura"),
 *     @OA\Property(property="primary_contact_name", type="string", nullable=true, example="María Pérez"),
 *     @OA\Property(property="primary_contact_email", type="string", nullable=true, example="maria@acme.com"),
 *     @OA\Property(property="phone", type="string", nullable=true, example="+58-412-0000000"),
 *     @OA\Property(property="notes", type="string", nullable=true, example="Cliente estratégico."),
 *     @OA\Property(property="address", type="string", nullable=true, example="Av. Libertador"),
 *     @OA\Property(property="city", type="string", nullable=true, example="Caracas"),
 *     @OA\Property(property="state", type="string", nullable=true, example="Distrito Capital"),
 *     @OA\Property(property="country", type="string", nullable=true, example="Venezuela"),
 *     @OA\Property(property="municipality", type="string", nullable=true, example="Libertador"),
 *     @OA\Property(property="parish", type="string", nullable=true, example="Altagracia"),
 *     @OA\Property(property="latitude", type="number", format="float", nullable=true, example=10.5022),
 *     @OA\Property(property="longitude", type="number", format="float", nullable=true, example=-66.9146)
 * )
 *
 * @OA\Schema(
 *     schema="ClientPayload",
 *     type="object",
 *     required={"name"},
 *     @OA\Property(property="name", type="string", maxLength=255, example="Acme Corp"),
 *     @OA\Property(property="industry", type="string", nullable=true, maxLength=255, example="Manufactura"),
 *     @OA\Property(property="primary_contact_name", type="string", nullable=true, maxLength=255, example="María Pérez"),
 *     @OA\Property(property="primary_contact_email", type="string", format="email", nullable=true, maxLength=255, example="maria@acme.com"),
 *     @OA\Property(property="phone", type="string", nullable=true, maxLength=255, example="+58-412-0000000"),
 *     @OA\Property(property="notes", type="string", nullable=true, example="Notas relevantes"),
 *     @OA\Property(property="address", type="string", nullable=true, maxLength=255, example="Av. Libertador"),
 *     @OA\Property(property="city", type="string", nullable=true, maxLength=255, example="Caracas"),
 *     @OA\Property(property="state", type="string", nullable=true, maxLength=255, example="Distrito Capital"),
 *     @OA\Property(property="country", type="string", nullable=true, maxLength=255, example="Venezuela"),
 *     @OA\Property(property="municipality", type="string", nullable=true, maxLength=255, example="Libertador"),
 *     @OA\Property(property="parish", type="string", nullable=true, maxLength=255, example="Altagracia"),
 *     @OA\Property(property="latitude", type="number", format="float", nullable=true, example=10.5022),
 *     @OA\Property(property="longitude", type="number", format="float", nullable=true, example=-66.9146)
 * )
 *
 * @OA\Schema(
 *     schema="ProjectResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=25),
 *     @OA\Property(property="client_id", type="integer", example=10),
 *     @OA\Property(property="client_name", type="string", nullable=true, example="Acme Corp"),
 *     @OA\Property(property="title", type="string", example="Transformación de procesos"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Proyecto de rediseño"),
 *     @OA\Property(property="status", type="string", example="en_diseno"),
 *     @OA\Property(property="type", type="string", nullable=true, example="consultoria"),
 *     @OA\Property(property="subtype", type="string", nullable=true, example="auditoria_fiscal"),
 *     @OA\Property(property="complexity", type="string", nullable=true, example="media"),
 *     @OA\Property(property="urgency", type="string", nullable=true, example="alta"),
 *     @OA\Property(property="estimated_budget", type="number", format="float", nullable=true, example=12000),
 *     @OA\Property(property="final_budget", type="number", format="float", nullable=true, example=14000),
 *     @OA\Property(property="currency", type="string", nullable=true, example="USD"),
 *     @OA\Property(property="priority_score", type="number", format="float", nullable=true, example=8.5),
 *     @OA\Property(property="priority_level", type="string", nullable=true, example="alta"),
 *     @OA\Property(property="assigned_to_id", type="integer", nullable=true, example=2),
 *     @OA\Property(property="validated_by_id", type="integer", nullable=true, example=1),
 *     @OA\Property(property="progress", type="integer", nullable=true, example=45),
 *     @OA\Property(property="starts_at_raw", type="string", nullable=true, example="2026-03-20"),
 *     @OA\Property(property="ends_at_raw", type="string", nullable=true, example="2026-06-20")
 * )
 *
 * @OA\Schema(
 *     schema="ProjectPayload",
 *     type="object",
 *     required={"client_id","title","status"},
 *     @OA\Property(property="client_id", type="integer", example=10),
 *     @OA\Property(property="title", type="string", maxLength=255, example="Transformación de procesos"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Detalle del proyecto"),
 *     @OA\Property(property="status", type="string", enum={"capturado","clasificacion_pendiente","priorizado","asignacion_lider_pendiente","en_diagnostico","en_diseno","en_implementacion","en_seguimiento","cerrado"}),
 *     @OA\Property(property="type", type="string", nullable=true, enum={"desarrollo_web","infraestructura","consultoria","soporte","mobile","otro"}),
 *     @OA\Property(property="subtype", type="string", nullable=true, maxLength=100, example="auditoria"),
 *     @OA\Property(property="complexity", type="string", nullable=true, enum={"baja","media","alta"}),
 *     @OA\Property(property="urgency", type="string", nullable=true, enum={"baja","media","alta"}),
 *     @OA\Property(property="estimated_budget", type="number", format="float", nullable=true, example=12000),
 *     @OA\Property(property="final_budget", type="number", format="float", nullable=true, example=14000),
 *     @OA\Property(property="currency", type="string", nullable=true, minLength=3, maxLength=3, example="USD"),
 *     @OA\Property(property="priority_score", type="number", format="float", nullable=true, minimum=1, maximum=10, example=8.5),
 *     @OA\Property(property="priority_level", type="string", nullable=true, enum={"baja","media","alta"}),
 *     @OA\Property(property="assigned_to", type="integer", nullable=true, example=2),
 *     @OA\Property(property="validated_by", type="integer", nullable=true, example=1),
 *     @OA\Property(property="progress", type="integer", nullable=true, minimum=0, maximum=100, example=45),
 *     @OA\Property(property="starts_at", type="string", format="date", nullable=true, example="2026-03-20"),
 *     @OA\Property(property="ends_at", type="string", format="date", nullable=true, example="2026-06-20"),
 *     @OA\Property(property="finished_at", type="string", format="date", nullable=true, example="2026-07-01")
 * )
 *
 * @OA\Schema(
 *     schema="SearchResultItem",
 *     type="object",
 *     @OA\Property(property="type", type="string", example="project"),
 *     @OA\Property(property="category", type="string", example="Proyectos"),
 *     @OA\Property(property="title", type="string", example="Transformación de procesos"),
 *     @OA\Property(property="subtitle", type="string", example="Acme Corp"),
 *     @OA\Property(property="url", type="string", example="/admin/projects"),
 *     @OA\Property(property="icon", type="string", example="ti ti-briefcase-2")
 * )
 *
 * @OA\Get(
 *     path="/",
 *     tags={"General"},
 *     summary="Landing pública de ETHOS",
 *     description="Entrega la vista principal del sitio sin autenticación.",
 *     @OA\Response(response=200, description="Vista HTML de landing")
 * )
 *
 * @OA\Get(
 *     path="/login",
 *     tags={"Auth"},
 *     summary="Formulario de login",
 *     description="Muestra el formulario de autenticación para usuarios invitados.",
 *     @OA\Response(response=200, description="Vista HTML de login"),
 *     @OA\Response(response=302, description="Redirección si ya existe sesión")
 * )
 *
 * @OA\Post(
 *     path="/login",
 *     tags={"Auth"},
 *     summary="Autenticar usuario",
 *     description="Procesa credenciales y abre sesión. Requiere token CSRF.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 required={"email","password"},
 *                 @OA\Property(property="email", type="string", format="email", example="miguel@ethos.com"),
 *                 @OA\Property(property="password", type="string", format="password", example="password"),
 *                 @OA\Property(property="remember", type="boolean", example=true)
 *             )
 *         )
 *     ),
 *     @OA\Response(response=302, description="Redirección al dashboard al autenticar"),
 *     @OA\Response(response=422, description="Validación fallida", @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse"))
 * )
 *
 * @OA\Post(
 *     path="/logout",
 *     tags={"Auth"},
 *     summary="Cerrar sesión",
 *     description="Cierra la sesión del usuario autenticado.",
 *     security={{"sessionAuth":{}},{"csrfHeader":{}}},
 *     @OA\Response(response=302, description="Redirección a /"),
 *     @OA\Response(response=401, description="No autenticado")
 * )
 *
 * @OA\Get(
 *     path="/register",
 *     tags={"Auth"},
 *     summary="Formulario de registro",
 *     @OA\Response(response=200, description="Vista HTML de registro")
 * )
 *
 * @OA\Post(
 *     path="/register",
 *     tags={"Auth"},
 *     summary="Registrar usuario",
 *     description="Registra un nuevo usuario. Requiere token CSRF.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 required={"name","email","password","password_confirmation"},
 *                 @OA\Property(property="name", type="string", example="Nuevo Usuario"),
 *                 @OA\Property(property="email", type="string", format="email", example="nuevo@ethos.com"),
 *                 @OA\Property(property="password", type="string", format="password", example="password"),
 *                 @OA\Property(property="password_confirmation", type="string", format="password", example="password")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=302, description="Redirección tras registro"),
 *     @OA\Response(response=422, description="Validación fallida", @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse"))
 * )
 *
 * @OA\Get(
 *     path="/admin/dashboard",
 *     tags={"Dashboard"},
 *     summary="Dashboard administrativo",
 *     description="Vista principal de métricas y actividad del CRM. Requiere auth, email verificado y permiso admin.access.",
 *     security={{"sessionAuth":{}}},
 *     @OA\Response(response=200, description="Vista HTML del dashboard"),
 *     @OA\Response(response=401, description="No autenticado"),
 *     @OA\Response(response=403, description="Sin permisos")
 * )
 *
 * @OA\Get(
 *     path="/admin/search",
 *     tags={"Admin Search"},
 *     summary="Búsqueda global del panel",
 *     description="Motor de búsqueda unificada para navegación, clientes y proyectos.",
 *     security={{"sessionAuth":{}}},
 *     @OA\Parameter(name="q", in="query", required=true, description="Texto de búsqueda (2 a 60 caracteres)", @OA\Schema(type="string", minLength=2, maxLength=60)),
 *     @OA\Parameter(name="category", in="query", required=false, description="Categoría de búsqueda", @OA\Schema(type="string", enum={"all","clients","projects","navigation"}, default="all")),
 *     @OA\Response(
 *         response=200,
 *         description="Resultados encontrados",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="query", type="string", example="acme"),
 *             @OA\Property(property="category", type="string", example="all"),
 *             @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/SearchResultItem"))
 *         )
 *     ),
 *     @OA\Response(response=422, description="Parámetros inválidos", @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse"))
 * )
 *
 * @OA\Get(
 *     path="/admin/clients",
 *     tags={"Clients"},
 *     summary="Listado de clientes",
 *     description="Retorna vista HTML con listado paginado de clientes.",
 *     security={{"sessionAuth":{}}},
 *     @OA\Response(response=200, description="Vista HTML"),
 *     @OA\Response(response=403, description="Sin permisos")
 * )
 *
 * @OA\Post(
 *     path="/admin/clients",
 *     tags={"Clients"},
 *     summary="Crear cliente",
 *     description="Crea cliente nuevo. Soporta respuesta HTML o JSON (si Accept: application/json).",
 *     security={{"sessionAuth":{}},{"csrfHeader":{}}},
 *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/ClientPayload")),
 *     @OA\Response(
 *         response=200,
 *         description="Cliente creado (modo JSON)",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Cliente creado exitosamente"),
 *             @OA\Property(property="client", ref="#/components/schemas/ClientResource")
 *         )
 *     ),
 *     @OA\Response(response=302, description="Redirección a listado (modo HTML)"),
 *     @OA\Response(response=422, description="Validación fallida", @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse"))
 * )
 *
 * @OA\Get(
 *     path="/admin/clients/markers",
 *     tags={"Clients"},
 *     summary="Marcadores geográficos de clientes",
 *     description="Devuelve clientes con coordenadas para visualización en mapa.",
 *     security={{"sessionAuth":{}}},
 *     @OA\Parameter(name="contact_type", in="query", required=false, @OA\Schema(type="string", enum={"primary","secondary"})),
 *     @OA\Parameter(name="status", in="query", required=false, @OA\Schema(type="string")),
 *     @OA\Parameter(name="type", in="query", required=false, @OA\Schema(type="string")),
 *     @OA\Response(
 *         response=200,
 *         description="Lista de marcadores",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="markers",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer"),
 *                     @OA\Property(property="title", type="string"),
 *                     @OA\Property(property="type", type="string", nullable=true),
 *                     @OA\Property(property="status", type="string", nullable=true),
 *                     @OA\Property(
 *                         property="position",
 *                         type="object",
 *                         @OA\Property(property="lat", type="number", format="float"),
 *                         @OA\Property(property="lng", type="number", format="float")
 *                     )
 *                 )
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/admin/clients/{client}",
 *     tags={"Clients"},
 *     summary="Detalle de cliente",
 *     description="Muestra detalle de cliente. Si se envía Accept: application/json retorna payload completo.",
 *     security={{"sessionAuth":{}}},
 *     @OA\Parameter(name="client", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Detalle HTML o JSON"),
 *     @OA\Response(response=404, description="Cliente no encontrado")
 * )
 *
 * @OA\Put(
 *     path="/admin/clients/{client}",
 *     tags={"Clients"},
 *     summary="Actualizar cliente (PUT)",
 *     description="Actualiza un cliente existente.",
 *     security={{"sessionAuth":{}},{"csrfHeader":{}}},
 *     @OA\Parameter(name="client", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/ClientPayload")),
 *     @OA\Response(response=200, description="Cliente actualizado (modo JSON)"),
 *     @OA\Response(response=302, description="Redirección (modo HTML)"),
 *     @OA\Response(response=422, description="Validación fallida", @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse"))
 * )
 *
 * @OA\Patch(
 *     path="/admin/clients/{client}",
 *     tags={"Clients"},
 *     summary="Actualizar cliente (PATCH)",
 *     description="Actualiza parcialmente un cliente.",
 *     security={{"sessionAuth":{}},{"csrfHeader":{}}},
 *     @OA\Parameter(name="client", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/ClientPayload")),
 *     @OA\Response(response=200, description="Cliente actualizado (modo JSON)"),
 *     @OA\Response(response=302, description="Redirección (modo HTML)")
 * )
 *
 * @OA\Get(
 *     path="/admin/projects",
 *     tags={"Projects"},
 *     summary="Listado de proyectos",
 *     security={{"sessionAuth":{}}},
 *     @OA\Response(response=200, description="Vista HTML de proyectos"),
 *     @OA\Response(response=403, description="Sin permisos")
 * )
 *
 * @OA\Post(
 *     path="/admin/projects",
 *     tags={"Projects"},
 *     summary="Crear proyecto",
 *     description="Crea un nuevo proyecto ligado a un cliente.",
 *     security={{"sessionAuth":{}},{"csrfHeader":{}}},
 *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/ProjectPayload")),
 *     @OA\Response(
 *         response=200,
 *         description="Proyecto creado (modo JSON)",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Proyecto creado exitosamente"),
 *             @OA\Property(property="project", ref="#/components/schemas/ProjectResource")
 *         )
 *     ),
 *     @OA\Response(response=302, description="Redirección a listado (modo HTML)"),
 *     @OA\Response(response=422, description="Validación fallida", @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse"))
 * )
 *
 * @OA\Get(
 *     path="/admin/projects/{project}",
 *     tags={"Projects"},
 *     summary="Detalle de proyecto",
 *     description="Retorna JSON con detalle extendido cuando se solicita como JSON.",
 *     security={{"sessionAuth":{}}},
 *     @OA\Parameter(name="project", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Detalle JSON o redirección HTML"),
 *     @OA\Response(response=404, description="Proyecto no encontrado")
 * )
 *
 * @OA\Put(
 *     path="/admin/projects/{project}",
 *     tags={"Projects"},
 *     summary="Actualizar proyecto (PUT)",
 *     security={{"sessionAuth":{}},{"csrfHeader":{}}},
 *     @OA\Parameter(name="project", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/ProjectPayload")),
 *     @OA\Response(response=200, description="Proyecto actualizado (modo JSON)"),
 *     @OA\Response(response=302, description="Redirección (modo HTML)"),
 *     @OA\Response(response=422, description="Validación fallida", @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse"))
 * )
 *
 * @OA\Patch(
 *     path="/admin/projects/{project}",
 *     tags={"Projects"},
 *     summary="Actualizar proyecto (PATCH)",
 *     security={{"sessionAuth":{}},{"csrfHeader":{}}},
 *     @OA\Parameter(name="project", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/ProjectPayload")),
 *     @OA\Response(response=200, description="Proyecto actualizado (modo JSON)"),
 *     @OA\Response(response=302, description="Redirección (modo HTML)")
 * )
 *
 * @OA\Get(
 *     path="/admin/clients/create",
 *     tags={"Clients"},
 *     summary="Formulario de creación de cliente",
 *     security={{"sessionAuth":{}}},
 *     @OA\Response(response=200, description="Vista HTML"),
 *     @OA\Response(response=403, description="Sin permisos")
 * )
 *
 * @OA\Get(
 *     path="/admin/clients/{client}/edit",
 *     tags={"Clients"},
 *     summary="Formulario de edición de cliente",
 *     security={{"sessionAuth":{}}},
 *     @OA\Parameter(name="client", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Vista HTML"),
 *     @OA\Response(response=404, description="Cliente no encontrado")
 * )
 *
 * @OA\Get(
 *     path="/admin/projects/create",
 *     tags={"Projects"},
 *     summary="Formulario de creación de proyecto",
 *     security={{"sessionAuth":{}}},
 *     @OA\Response(response=200, description="Vista HTML"),
 *     @OA\Response(response=403, description="Sin permisos")
 * )
 *
 * @OA\Get(
 *     path="/admin/projects/{project}/edit",
 *     tags={"Projects"},
 *     summary="Formulario de edición de proyecto",
 *     security={{"sessionAuth":{}}},
 *     @OA\Parameter(name="project", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Vista HTML"),
 *     @OA\Response(response=404, description="Proyecto no encontrado")
 * )
 *
 * @OA\Get(
 *     path="/forgot-password",
 *     tags={"Auth"},
 *     summary="Formulario para solicitar recuperación de contraseña",
 *     @OA\Response(response=200, description="Vista HTML")
 * )
 *
 * @OA\Post(
 *     path="/forgot-password",
 *     tags={"Auth"},
 *     summary="Enviar enlace de recuperación de contraseña",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 required={"email"},
 *                 @OA\Property(property="email", type="string", format="email", example="admin@ethos.com")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=302, description="Redirección con estado"),
 *     @OA\Response(response=422, description="Validación fallida")
 * )
 *
 * @OA\Get(
 *     path="/reset-password/{token}",
 *     tags={"Auth"},
 *     summary="Formulario de restablecimiento de contraseña",
 *     @OA\Parameter(name="token", in="path", required=true, @OA\Schema(type="string")),
 *     @OA\Response(response=200, description="Vista HTML")
 * )
 *
 * @OA\Get(
 *     path="/sanctum/csrf-cookie",
 *     tags={"API"},
 *     summary="Obtener cookie CSRF para Sanctum",
 *     description="Inicializa cookies XSRF-TOKEN y laravel_session para clientes SPA.",
 *     @OA\Response(response=204, description="Cookie CSRF emitida")
 * )
 *
 * @OA\Post(
 *     path="/reset-password",
 *     tags={"Auth"},
 *     summary="Restablecer contraseña",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 required={"token","email","password","password_confirmation"},
 *                 @OA\Property(property="token", type="string"),
 *                 @OA\Property(property="email", type="string", format="email"),
 *                 @OA\Property(property="password", type="string", format="password"),
 *                 @OA\Property(property="password_confirmation", type="string", format="password")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=302, description="Redirección tras reset"),
 *     @OA\Response(response=422, description="Validación fallida")
 * )
 *
 * @OA\Get(
 *     path="/verify-email",
 *     tags={"Auth"},
 *     summary="Aviso de verificación de email",
 *     security={{"sessionAuth":{}}},
 *     @OA\Response(response=200, description="Vista HTML"),
 *     @OA\Response(response=401, description="No autenticado")
 * )
 *
 * @OA\Get(
 *     path="/verify-email/{id}/{hash}",
 *     tags={"Auth"},
 *     summary="Verificar email de usuario",
 *     security={{"sessionAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Parameter(name="hash", in="path", required=true, @OA\Schema(type="string")),
 *     @OA\Response(response=302, description="Email verificado y redirección"),
 *     @OA\Response(response=403, description="Hash o firma inválida")
 * )
 *
 * @OA\Post(
 *     path="/email/verification-notification",
 *     tags={"Auth"},
 *     summary="Reenviar notificación de verificación",
 *     security={{"sessionAuth":{}},{"csrfHeader":{}}},
 *     @OA\Response(response=302, description="Notificación reenviada"),
 *     @OA\Response(response=401, description="No autenticado")
 * )
 *
 * @OA\Get(
 *     path="/confirm-password",
 *     tags={"Auth"},
 *     summary="Formulario de confirmación de contraseña",
 *     security={{"sessionAuth":{}}},
 *     @OA\Response(response=200, description="Vista HTML")
 * )
 *
 * @OA\Post(
 *     path="/confirm-password",
 *     tags={"Auth"},
 *     summary="Confirmar contraseña actual",
 *     security={{"sessionAuth":{}},{"csrfHeader":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 required={"password"},
 *                 @OA\Property(property="password", type="string", format="password")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=302, description="Contraseña confirmada"),
 *     @OA\Response(response=422, description="Contraseña inválida")
 * )
 *
 * @OA\Put(
 *     path="/password",
 *     tags={"Auth"},
 *     summary="Actualizar contraseña de usuario autenticado",
 *     security={{"sessionAuth":{}},{"csrfHeader":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 required={"current_password","password","password_confirmation"},
 *                 @OA\Property(property="current_password", type="string", format="password"),
 *                 @OA\Property(property="password", type="string", format="password"),
 *                 @OA\Property(property="password_confirmation", type="string", format="password")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=302, description="Contraseña actualizada"),
 *     @OA\Response(response=422, description="Validación fallida")
 * )
 *
 * @OA\Get(
 *     path="/profile",
 *     tags={"Profile"},
 *     summary="Formulario de perfil",
 *     description="Muestra pantalla de edición del perfil autenticado.",
 *     security={{"sessionAuth":{}}},
 *     @OA\Response(response=200, description="Vista HTML"),
 *     @OA\Response(response=401, description="No autenticado")
 * )
 *
 * @OA\Patch(
 *     path="/profile",
 *     tags={"Profile"},
 *     summary="Actualizar perfil de usuario",
 *     security={{"sessionAuth":{}},{"csrfHeader":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 required={"name","email"},
 *                 @OA\Property(property="name", type="string", example="Admin"),
 *                 @OA\Property(property="email", type="string", format="email", example="admin@ethos.com")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=302, description="Redirección a /profile con estado profile-updated"),
 *     @OA\Response(response=422, description="Validación fallida", @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse"))
 * )
 *
 * @OA\Delete(
 *     path="/profile",
 *     tags={"Profile"},
 *     summary="Eliminar cuenta autenticada",
 *     description="Requiere password actual para confirmar eliminación.",
 *     security={{"sessionAuth":{}},{"csrfHeader":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 required={"password"},
 *                 @OA\Property(property="password", type="string", format="password", example="password")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=302, description="Redirección a / tras eliminar cuenta"),
 *     @OA\Response(response=422, description="Password inválida")
 * )
 *
 * @OA\Get(
 *     path="/api/user",
 *     tags={"API"},
 *     summary="Obtener usuario autenticado (Sanctum)",
 *     description="Endpoint API protegido por auth:sanctum.",
 *     security={{"sanctumBearer":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Usuario autenticado",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="Admin"),
 *             @OA\Property(property="email", type="string", example="admin@ethos.com")
 *         )
 *     ),
 *     @OA\Response(response=401, description="No autenticado")
 * )
 */
class ApiDocumentation
{
}

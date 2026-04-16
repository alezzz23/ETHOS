# ETHOS — Cómo funciona el sistema (flujo completo)

> Este documento explica en lenguaje sencillo cómo funciona ETHOS: quién hace qué, cuándo y por qué. Está pensado para que cualquier persona del equipo entienda el flujo sin necesidad de revisar el código.

---

## Los personajes del sistema

Antes de hablar del flujo, hay que conocer a los actores. Cada persona que entra al sistema tiene un **rol**, y ese rol define exactamente qué puede ver y tocar.

### Super Admin
Es el administrador total. Puede hacer absolutamente todo: crear usuarios, asignar roles, ver y editar cualquier proyecto, cliente o propuesta. No tiene restricciones. Se usa para configuración inicial y emergencias.

### Marketing (Captación)
Es quien tiene el primer contacto con el cliente. En la configuración actual puede **crear y editar clientes** y consultar proyectos y servicios. La captura de proyectos depende del permiso `projects.create`, que hoy no está asignado al rol marketing en la demo.

> Puede ver: clientes, proyectos (vista general), servicios.  
> Puede hacer: crear y editar clientes.

### Consultor
Es el cerebro técnico. Una vez que el proyecto llega al sistema, el consultor lo analiza: decide qué servicio aplica, cuántas horas tomará, a qué precio y quién va a liderarlo. También puede crear **propuestas formales** para presentarle al cliente.

> Puede ver: clientes, proyectos, servicios, propuestas.  
> Puede hacer: analizar proyectos, asignar servicios y líderes, crear y editar propuestas.

### Líder de Proyecto
Una vez aprobado el proyecto, el líder es quien normalmente conduce la ejecución en el campo. Gestiona tareas, puede aprobar o rechazar propuestas formales y participa en el seguimiento operativo del proyecto.

> Puede ver: proyectos, tareas, propuestas.  
> Puede hacer: gestionar tareas, aprobar propuestas y editar proyectos.

### Equipo de Levantamiento
Es el equipo operativo que trabaja directamente en el proyecto. Solo puede ver los proyectos que les corresponden y ejecutar las tareas asignadas. No tiene acceso a configuraciones ni datos financieros.

> Puede ver: proyectos asignados.  
> Puede hacer: ejecutar tareas.

---

## El flujo de un proyecto, paso a paso

Un proyecto en ETHOS pasa por **5 fases** bien definidas, y cada una tiene un responsable claro.

---

### FASE 1 — Captura (`capturado`)
**Quién:** Cualquier usuario con permiso `projects.create`

Alguien detecta una oportunidad de negocio con un cliente. Entra al sistema y crea el proyecto con los datos básicos:

- Nombre del cliente
- Título del proyecto
- Descripción general de lo que se necesita
- Tipo y subtipo (ej: consultoría / procesos operativos)
- Urgencia (baja, media, alta)
- Complejidad estimada
- Presupuesto aproximado que tiene el cliente
- Fecha tentativa de inicio

Cuando se guarda, el sistema hace tres cosas automáticamente:

1. **Bloquea los campos básicos** del proyecto para que nadie los pueda modificar sin privilegios especiales (se protege la información original del cliente).
2. **Crea una tarea** para cada consultor disponible con el mensaje "Analizar viabilidad y cotizar servicio", con vencimiento a 3 días hábiles.
3. **Notifica a los consultores** por área funcional. Si el proyecto es de consultoría de RRHH, se notifica a consultores que trabajen en esa área. Si no hay nadie específico, se notifica a todos.

> Si una tarea de análisis lleva más de 48 horas sin atención, el sistema la **escala automáticamente** a los usuarios con rol de líder de proyecto. Esa revisión corre de forma programada cada hora.

---

### FASE 2 — Análisis (`en_analisis`)
**Quién:** Consultor

El consultor entra al detalle del proyecto, lo revisa y lo complementa con información técnica y comercial:

- Selecciona el **servicio** que se va a ofrecer
- Asigna el **líder de proyecto** que conducirá la ejecución
- Define las **horas estimadas** de trabajo
- Define la **tarifa por hora**
- Puede asignar opcionalmente un consultor de apoyo

En esta fase también se puede crear una **Propuesta formal** (ver sección más abajo).

---

### FASE 3 — Aprobación (`aprobado`)
**Quién:** Cualquier usuario con permiso `projects.edit` como consultor, líder de proyecto o super admin

Una vez analizado el proyecto, alguien con permiso `projects.edit` puede aprobarlo y definir opcionalmente la fecha estimada de cierre. El proyecto no exige tener una propuesta aprobada para pasar a `aprobado`, pero si ya existe una propuesta aprobada asociada, esa propuesta se usa para generar el checklist de levantamiento.

El sistema hace automáticamente:

1. Registra la fecha y hora exacta de aprobación.
2. Recalcula la prioridad del proyecto según urgencia, complejidad y estado del cliente.
3. Notifica al líder de proyecto asignado.
4. Si existe una propuesta aprobada y todavía no hay checklist, genera el checklist automático con base en la propuesta aprobada más reciente.

---

### FASE 4 — Ejecución (`en_ejecucion`)
**Quién:** Normalmente el líder de proyecto, o cualquier usuario con permiso `projects.edit`

El inicio de ejecución registra ese momento exacto en el sistema. A partir de aquí se registra el avance con entradas que incluyen:

- **Método de trabajo:** encuesta, entrevista, observación o revisión documental
- **Fase:** levantamiento, diagnóstico, propuesta, implementación o seguimiento
- **Horas planificadas** vs **horas reales** trabajadas
- Porcentaje de avance logrado
- Notas del trabajo realizado
- Vínculo opcional a un ítem del checklist

**Automatizaciones en esta fase:**

- Si una entrada llega al 100% vinculada a un ítem del checklist, ese ítem se **marca completado automáticamente**.
- Si todos los ítems del checklist quedan completos, el checklist **se cierra solo**.
- Si las horas reales acumuladas superan en más del **20%** lo estimado, el sistema **envía una alerta** al consultor y al líder para que intervengan a tiempo.

---

### FASE 5 — Cierre (`cerrado`)
**Quién:** Cualquier usuario con permiso `projects.edit`

Cuando el proyecto terminó, se marca como cerrado registrando el presupuesto final y la fecha real de entrega. El sistema:

1. Registra la fecha y hora exacta de cierre.
2. Programa la creación de una **encuesta de satisfacción** una hora después del cierre. La encuesta queda asociada al cliente con un token único que expira en 30 días y puede responderse sin tener cuenta en el sistema.

---

## El módulo de Propuestas

Las propuestas son documentos formales que se le presentan al cliente antes de aprobar el trabajo. Se crean dentro de un proyecto durante la fase de análisis.

**Ciclo de vida:**

```
borrador  →  enviada  →  aprobada
                      ↘  rechazada
```

- **Borrador:** El consultor construye la propuesta definiendo servicio, horas, tarifa, margen de ganancia, tamaño del cliente e hitos de pago. El sistema calcula automáticamente el rango de precio mínimo y máximo.
- **Enviada:** El consultor la marca como enviada al cliente. A partir de aquí puede ser aprobada o rechazada por alguien con permiso `proposals.approve`.
- **Aprobada:** Se registra quién la aprobó y cuándo. Además, el proyecto pasa a `aprobado` y se genera la lista de levantamiento para esa propuesta.
- **Rechazada:** Se registra el motivo. La propuesta queda en el historial con ese rechazo documentado.

En cualquier estado, la propuesta puede generarse como **PDF** para compartir con el cliente.

---

## Automatizaciones del sistema

| Evento | Lo que ocurre automáticamente |
|---|---|
| Se crea un proyecto | Campos bloqueados, tareas creadas para consultores, notificaciones enviadas |
| Tarea sin atender +48h | Escalada al líder con alerta |
| Propuesta pasa a `approved` | Se registra la aprobación, el proyecto pasa a `aprobado` y se genera el checklist de levantamiento |
| Proyecto pasa a `aprobado` | Prioridad recalculada, líder notificado y, si hay una propuesta aprobada y no existe checklist, se genera uno |
| Proyecto pasa a `en_ejecucion` | Timestamp de inicio registrado |
| Horas reales superan 20% de desvío | Alerta al consultor y al líder |
| Ítem de checklist llega a 100% vinculado a progreso | Ítem marcado completado |
| Todos los ítems del checklist completos | Checklist cerrado automáticamente |
| Proyecto cerrado | Encuesta de satisfacción creada en segundo plano 1h después con token público |

---

## Bloqueo de campos

Al crear un proyecto, estos campos quedan **protegidos** para preservar la información original del cliente:

- Título y descripción
- Tipo y subtipo
- Urgencia y complejidad
- Fecha de inicio estimada
- Presupuesto estimado

Actualmente solo un `super_admin` o un `consultor` pueden modificarlos después de la captura. Esto evita que alguien altere lo que el cliente comunicó originalmente.

---

## Usuarios del sistema (Demo)

| Nombre | Email | Rol |
|---|---|---|
| Miguel | admin@ethos.com | super_admin |
| Ana | marketing@ethos.com | marketing |
| Carlos | consultor@ethos.com | consultor |
| Laura | lider@ethos.com | lider_proyecto |
| Pedro | equipo@ethos.com | equipo_levantamiento |

> Contraseña por defecto en demo: `password`
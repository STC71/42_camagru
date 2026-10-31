<?php

/*
	Aquí se encuentra el punto de entrada de la aplicación. 
	Todas las solicitudes pasan por este archivo, que se encarga de iniciar la sesión, 
	cargar la configuración y las clases necesarias, y finalmente despachar la solicitud al enrutador.
	
	Es importante cargar la configuración antes de cualquier otra cosa, ya que muchas clases dependen de ella.
	
	Las clases se encargan de manejar la base de datos, el enrutamiento, el envío de correos electrónicos 
	y la validación de datos. Son como los cimientos de la aplicación, sobre los cuales se construye toda 
	la lógica de negocio. Una clase es una plantilla para crear objetos, algo así como un plano para construir 
	una casa. Cada clase tiene sus propios métodos y propiedades, que son como las herramientas y materiales 
	que se utilizan para construir la casa.
	
	El enrutador es el encargado de recibir la solicitud del usuario y decidir qué controlador y acción deben 
	manejarla. Es como un director de orquesta, que coordina a los músicos para que toquen la música correcta 
	en el momento adecuado.
*/

// Iniciar sesion
session_start();
/* 
	session_start() es una función de PHP que inicia una nueva sesión o reanuda una sesión existente. 
	Esta función debe ser llamada antes de cualquier salida de datos, incluyendo etiquetas HTML o 
	caracteres de espacio. 
	Es como abrir una caja fuerte: necesitas la llave (la sesión) para acceder a su contenido 
	(los datos de la sesión). En concreto le dice a PHP que debe buscar un identificador de sesión 
	en la solicitud del usuario (por ejemplo, en una cookie) y, si lo encuentra, cargar los datos 
	de la sesión correspondiente.
	Un identificador de sesión es un número único que PHP asigna a cada sesión. Algo así como un 
	número de serie en una fábrica, que permite identificar un producto específico.
*/

// Cargar configuracion
require_once '../config/Config.php';
/* 
	require_once es una construcción de PHP que incluye y evalúa el archivo especificado durante 
	la ejecución del script. Es como decirle a PHP: 
	"Oye, necesito que leas este libro antes de continuar con la historia, por que en Config.php 
	se encuentran las configuraciones de la aplicación que vamos a necesitar más adelante".
	Si el archivo no se encuentra, se genera un error fatal y la ejecución del script se detiene. 
*/
Config::load();
/* 
	La clase Config es responsable de cargar y almacenar la configuración de la aplicación.  
	El método load() lee el archivo de configuración y almacena los valores en una estructura de datos 
	que puede ser accedida por otras partes de la aplicación. En concreto load() usará la función 
	require_once anterior para incluir el archivo de configuración Config.php y luego almacenará los 
	valores en la estructura.
	Siguiendo con nuestra analogía, es como abrir el armario y sacar las herramientas que necesitamos 
	para construir la casa. load sería como nuestro asistente que nos ayuda a encontrar las herramientas 
	correctas y nos las entrega cuando las necesitamos, mientras que Config sería el armario donde guardamos 
	todas las herramientas.
*/

// Cargar clases core
require_once '../app/core/Database.php';
/* 
	Aquí require_once se utiliza para incluir la clase Database, que es responsable de manejar la conexión 
	a la base de datos. La clase Database es como un puente entre la aplicación y la base de datos. 
	Se encarga de establecer la conexión, ejecutar consultas y manejar los resultados. 
	Es como un traductor que permite a la aplicación comunicarse con la base de datos en un lenguaje que 
	ambos entienden.
*/
require_once '../app/core/Controller.php';
/* 
	La clase Controller es la base para todos los controladores de la aplicación. Un controlador es responsable 
	de manejar las solicitudes del usuario, interactuar con el modelo y devolver la respuesta adecuada. 
	Es como un director de cine que coordina a los actores (modelos) y decide qué escenas (vistas) se deben 
	grabar para contar la historia (respuesta al usuario).
*/
require_once '../app/core/Router.php';
/* 
	La clase Router es responsable de recibir la solicitud del usuario y decidir qué controlador y acción 
	deben manejarla. 
	Por ejemplo, si un usuario solicita la URL /users/profile, el enrutador decidirá que el controlador 
	UsersController y la acción profile deben manejar la solicitud.
	Es como un director de orquesta, que coordina a los músicos para que toquen la música 
	correcta en el momento adecuado.
*/
require_once '../app/core/Mailer.php';
/* 
	La clase Mailer es responsable de enviar correos electrónicos desde la aplicación. 
	Es como un cartero que entrega cartas (correos electrónicos) a los destinatarios correctos.
*/
require_once '../app/core/Validator.php';
/* 
	La clase Validator es responsable de validar los datos de entrada del usuario. 
	Es como un inspector de calidad que verifica que los productos (datos) cumplen con los estándares 
	requeridos antes de ser enviados al cliente (aplicación).
	Por ejemplo, si un usuario introduce una contraseña, el validador puede comprobar que cumple con 
	los requisitos de longitud y complejidad antes de permitir que se guarde en la base de datos.
*/

// Multi-idioma (por defecto español) — no afecta a rutas ni a la lógica de negocio
require_once '../app/core/I18n.php';
/* 
	La clase I18n es responsable de manejar la internacionalización de la aplicación. 
	Esto significa que permite que la aplicación se muestre en diferentes idiomas según las preferencias del usuario. 
	Es como un traductor que convierte el contenido de la aplicación a diferentes idiomas para que los usuarios 
	puedan entenderlo.
	Por ejemplo, si un usuario prefiere ver la aplicación en inglés, I18n se encargará de traducir todos los 
	textos y mensajes al inglés antes de mostrarlos al usuario.
*/
I18n::init();
/* 
	El método init() de la clase I18n inicializa la configuración de internacionalización. 
	Esto puede incluir la carga de archivos de traducción, la detección del idioma preferido del usuario 
	y la configuración de las funciones de traducción.
	Es como preparar el traductor para que esté listo para traducir el contenido de la aplicación al idioma 
	preferido del usuario.
*/

// Inicializar router y enviar solicitud
$router = new Router();
/* 
	Se crea una instancia de la clase Router, que es responsable de recibir la solicitud del usuario y decidir 
	qué controlador y acción deben manejarla. 
	Una instancia es algo así como una copia de un plano (clase) que se puede utilizar para construir un objeto 
	específico. Usamos una copia, instancia, para que podamos tener múltiples objetos (enrutadores) que funcionen 
	de manera independiente entre sí.
	Es como si un arquitecto (clase) tuviera un plano para construir una casa (objeto). Cada vez que el arquitecto 
	construye una casa, está utilizando el mismo plano, pero cada casa es un objeto independiente que puede tener 
	sus propias características y detalles como el color de la pintura o el tipo de ventanas.
*/
$router->dispatch();
/* 
	El método dispatch() de la clase Router recibe la solicitud del usuario y decide qué controlador y acción 
	deben manejarla. 
	dispatch significa "despachar" o "enviar". En este contexto, significa que el enrutador está enviando 
	la solicitud al controlador y acción correctos para que se maneje. ¿Podemos cambiar dispatch por despachar? 
	Sí, pero en inglés es más común usar dispatch en el contexto de enrutamiento y manejo de solicitudes.
	¿Qué hace el -> ? Es el operador de acceso a miembros en PHP. Se utiliza para acceder a métodos y propiedades 
	de un objeto. Algo así si en un almacen (clase) tenemos un carrito de compras (objeto) y queremos acceder 
	a la función llamada "calcularTotal" que calcula el total de la compra (método), usaríamos el operador -> 
	para decir "quiero usar esta función en este carrito de compras específico".
*/

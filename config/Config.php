<?php
/*  <?php sirve para indicar que el código que sigue es código PHP. 
    Es la etiqueta de apertura para un script PHP.  */

/**
 * Config Class
 * Descripción: Clase de configuración para cargar variables de entorno desde un archivo .env
 * y proporcionar un método estático para acceder a ellas.
 */
class Config {
/*  Con class Config se define una clase llamada Config. 
    Esta clase se utiliza para manejar la configuración de la aplicación, 
    específicamente para cargar variables de entorno desde un archivo .env 
//  y proporcionar un método estático para acceder a ellas.  */

    private static $config = [];
    /*  Con private static $config = []; se declara una propiedad estática privada llamada $config, 
        que es un array vacío.
        Una propiedad es una variable que pertenece a una clase.
        Una propiedad estática es una variable que pertenece a la clase en sí en lugar de a una 
        instancia específica de la clase. De esta manera, $config es compartida por todas las instancias 
        de la clase Config y puede ser accedida sin necesidad de crear una instancia de la clase. 
        Al ser privada, esta propiedad solo puede ser accedida desde dentro de la clase Config, lo que 
        ayuda a encapsular, o sea proteger la información y evitar que sea modificada directamente desde 
        fuera de la clase.
        En este caso, $config se utiliza para almacenar las variables de entorno cargadas desde el archivo 
        .env. El signo de dólar ($) en php se utiliza para indicar que lo que sigue es una variable. 
        En este caso, $config es una variable de clase (propiedad) que se utiliza para almacenar la 
        configuración cargada desde el archivo .env. 
        Al ser estática, se accede a ella mediante self::$config dentro de la clase.
    */

    public static function load() {
    /*  Con public static function load() se define un método estático público llamado load.
        Un método es una función que pertenece a una clase.
        Un método estático es una función que pertenece a la clase en sí en lugar de a una instancia 
        específica de la clase. Esto significa que se puede llamar a este método sin necesidad de crear
        una instancia de la clase Config. Al ser público, este método puede ser accedido desde 
        cualquier parte del código que tenga acceso a la clase Config. 
        En este caso, el método load se utiliza para cargar las variables de entorno desde un archivo 
        .env
        En resumen, esta línea define un método llamado load que es estático y público  */

        $envFile = __DIR__ . '/../../.env';
        /*  Con $envFile = __DIR__ . '/../../.env'; se define una variable local llamada $envFile 
            que contiene la ruta al archivo .env.
            __DIR__ es una constante mágica en PHP que devuelve el directorio del archivo actual. 
            En este caso, se utiliza para construir la ruta al archivo .env que se encuentra dos 
            niveles por encima del directorio actual. La ruta resultante se almacena en la variable 
            $envFile, que luego se utiliza para cargar las variables de entorno desde ese archivo.  */

        if (file_exists($envFile)) {
            /*  file_exists($envFile) es una función de PHP que verifica si un archivo existe en la 
                ruta especificada. En este caso, se utiliza para comprobar si el archivo .env existe 
                en la ruta definida por $envFile en la línea anterior.  */

            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            /*  Con $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); se lee el 
                contenido del archivo .env que guardamos en $envFile y se almacena en la variable 
                $lines como un array de líneas. 
                La función file() lee un archivo y devuelve su contenido como un array, donde cada 
                elemento del array corresponde a una línea del archivo. 
                Los parámetros FILE_IGNORE_NEW_LINES y FILE_SKIP_EMPTY_LINES se utilizan para ignorar 
                las nuevas líneas al final de cada línea y para omitir las líneas vacías, 
                respectivamente. Esto significa que $lines contendrá solo las líneas con contenido 
                del archivo .env, sin incluir las líneas vacías ni los caracteres de nueva línea.
                El | se utiliza para combinar las dos constantes FILE_IGNORE_NEW_LINES y 
                FILE_SKIP_EMPTY_LINES, lo que permite aplicar ambas opciones al mismo tiempo al leer 
                el archivo $envFile, antes pasar el contenido a la variable $lines.  */
            foreach ($lines as $line) {
                /*  Con foreach ($lines as $line) se inicia un bucle que itera sobre cada elemento del 
                    array $lines, asignando cada elemento a la variable $line en cada iteración. 
                    Esto permite procesar cada línea del archivo .env una por una dentro del bloque 
                    de código del bucle.  */
                if (strpos($line, '#') === 0) continue;
                /*  Con if (strpos($line, '#') === 0) continue; se verifica si la línea actual comienza 
                    con el carácter #, lo que indica que es un comentario en el archivo .env. 
                    La función strpos() busca la posición de la primera aparición de un substring 
                    (en este caso, '#') en una cadena (en este caso, $line). 
                    Si el resultado es 0, significa que el carácter # está al inicio de la línea. 
                    Si esta condición se cumple, se ejecuta continue, lo que hace que el bucle salte a 
                    la siguiente iteración sin procesar esa línea, ya que es un comentario y no contiene 
                    una variable de entorno válida. 
                    === se utiliza para comparar tanto el valor como el tipo de los operandos (los operandos
                    son $line y 0 en este caso), lo que garantiza que la comparación sea estricta.  */
                list($key, $value) = explode('=', $line, 2);
                /*  Con list($key, $value) = explode('=', $line, 2); se divide la línea actual en dos 
                    partes utilizando el carácter = como delimitador. 
                    La función explode() toma una cadena y la divide en un array utilizando el 
                    delimitador especificado, en este caso, '='. 
                    El tercer parámetro 2 indica que solo se deben dividir en dos partes, la la parte 
                    antes del = se asigna a $key y la parte después del = se asigna a $value. 
                    El tercer parámetro 2 indica que solo se deben dividir en dos partes, lo que es útil 
                    para manejar valores que puedan contener el carácter =   */
                self::$config[trim($key)] = trim($value);
                /*  Con self::$config[trim($key)] = trim($value); se almacena la clave y el valor en el 
                    array estático $config de la clase Config. 
                    trim() se utiliza para eliminar cualquier espacio en blanco al inicio o al final de 
                    la clave ($key) y del valor ($value) antes de almacenarlos en el array. Esto ayuda a 
                    asegurar que las claves y los valores se almacenen sin espacios adicionales que puedan 
                    causar problemas al acceder a ellos más tarde.  
                    self:: se utiliza para acceder a la propiedad estática $config dentro de la clase Config.
                    */
                putenv(trim($key) . '=' . trim($value));
                /*  Con putenv(trim($key) . '=' . trim($value)); se establece la variable de entorno en el 
                    entorno del sistema utilizando la función putenv(). 
                    La función putenv() toma una cadena en formato "clave=valor" y la agrega al entorno del 
                    sistema, lo que permite que estas variables de entorno estén disponibles para el resto de 
                    la aplicación. Al igual que antes, se utiliza trim() para eliminar cualquier espacio en 
                    blanco alrededor de la clave y el valor antes de establecer la variable de entorno.  */
            }
        }
    }

    /*  Con public static function get($key, $default = null) se define un método estático público llamado 
        get que acepta dos parámetros: $key, que es la clave de la variable de entorno que se desea obtener, 
        y $default, que es un valor opcional que se devuelve si la clave no se encuentra en la configuración.
        Este método se utiliza para acceder a las variables de entorno cargadas en la clase Config.
    */
    public static function get($key, $default = null) {
        return self::$config[$key] ?? getenv($key) ?: $default;
        /*  Con return self::$config[$key] ?? getenv($key) ?: $default; se devuelve el valor de la 
            variable de entorno correspondiente a la clave $key. 
            Primero, se intenta obtener el valor de la variable de entorno desde el array estático 
            $config utilizando self::$config[$key]. 
            getenv($key) se utiliza para obtener el valor de la variable de entorno desde el entorno del 
            sistema si no se encuentra en el array $config.
            ?? equivale a un if que verifica si self::$config[$key] existe y no es null. 
            Si existe, se devuelve ese valor.
            ?: es un operador ternario que se utiliza para devolver $default si getenv($key) también devuelve 
            un valor falso (como false o null). 
            En resumen, este método devuelve el valor de la variable de entorno correspondiente a la clave 
            $key, o un valor predeterminado si la clave no se encuentra en la configuración ni en el 
            entorno del sistema.  */
    }
}

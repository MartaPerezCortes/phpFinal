<?php
    define('HOST', '127.0.0.1');
    define('USER', 'root');
    define('PASS', '');
    define('DB', 'db24');

    class DAO{
		private $conexion; 
        private $error;
				
		public function __construct(){ //constructor
			$this->conexion= new mysqli(HOST,USER,PASS,DB);
			if($this->conexion->connect_errno){
				die('Error de conexión: '.$this->conexion->connect_error);
			}
			$this->error='';
		}

        public function consultar($SQL){
			//NO VISUALIZAR NADA AQUI PUES NO RETURN-ARA BIEN
			$res=$this->conexion->query($SQL,MYSQLI_USE_RESULT); //sin usar buffer mysql
			$filas=array();
			if ($this->conexion->errno) {
					die('Error en consulta: '.$this->conexion->error.' SQL: <b>'.$SQL.'</b>');
			}else{
				while($reg=$res->fetch_assoc()){
					$filas[]=$reg;
				}
			}

            // Depuración: Log de cantidad de filas obtenidas
            error_log("Filas obtenidas en consultar: " . count($filas));

			return $filas;
		}

		public function insertar($SQL){
            $this->conexion->query($SQL);
            if ($this->conexion->error) {
                error_log('Error en consulta SQL de inserción: ' . $this->conexion->error . ' SQL: ' . $SQL);
                return false;
            } else {
                return $this->conexion->insert_id;
            }
        }

        public function actualizar($SQL){
            $this->conexion->query($SQL, MYSQLI_USE_RESULT);
            if($this->conexion->connect_errno){
                die('Error consulat a BD: '.$SQL);
                return '';
            }else{
                return $this->conexion->affected_rows;
            }
        }

        public function borrar($SQL){
			$this->conexion->query($SQL);
			return $this->conexion->affected_rows;
		}

        public function ejecutar($SQL) {
            $resultado = $this->conexion->query($SQL);
            return $resultado;
        }

        // Método para consultas SELECT con parámetros
        public function consultarConParametros($SQL, $parametros) {
            if (!is_array($parametros)) {
                error_log("❌ Error: Se esperaba un array, pero se recibió " . gettype($parametros));
                return [];
            }
        
            if (count($parametros) === 0) {
                error_log("⚠️ Advertencia: Se llamó a consultarConParametros() sin parámetros.");
                return [];
            }
        
            $stmt = $this->conexion->prepare($SQL);
            if ($stmt === false) {
                error_log("❌ Error al preparar la consulta: " . $this->conexion->error);
                return [];
            }
        
            // Detectar tipos dinámicamente
            $tipos = "";
            foreach ($parametros as $parametro) {
                if (is_int($parametro)) {
                    $tipos .= "i"; // Entero
                } elseif (is_double($parametro)) {
                    $tipos .= "d"; // Decimal
                } elseif (is_string($parametro)) {
                    $tipos .= "s"; // String
                } else {
                    $tipos .= "b"; // Blob u otro tipo
                }
            }
        
            // Asociar los parámetros
            $stmt->bind_param($tipos, ...$parametros);
            $stmt->execute();
            $resultado = $stmt->get_result();
        
            $filas = [];
            while ($fila = $resultado->fetch_assoc()) {
                $filas[] = $fila;
            }
        
            if (empty($filas)) {
                error_log("⚠️ La consulta no devolvió resultados: $SQL con parámetros " . json_encode($parametros));
            }
        
            $stmt->close();
            return $filas;
        }
        
        
        

        // Método para consultas INSERT, UPDATE o DELETE con parámetros
        public function actualizarConParametros($SQL, $parametros) {
            $stmt = $this->conexion->prepare($SQL);
            if ($stmt === false) {
                die('Error al preparar la consulta: ' . $this->conexion->error);
            }

            // Generar dinámicamente los tipos de parámetros
            $tipos = str_repeat('s', count($parametros)); // Asume que todos los parámetros son strings. Cambia a 'i' si son enteros.

            $stmt->bind_param($tipos, ...$parametros);
            $stmt->execute();

            $resultado = $stmt->affected_rows;
            $stmt->close();

            return $resultado>0;
        }

        
    }
?>
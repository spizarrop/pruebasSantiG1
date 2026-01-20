<?php
    require_once __DIR__.'/../config/conexion.php';
    
    class ModAsociacion extends Conexion {

        function __construct() {
            parent::__construct(); 
        }

        public function listar() {
            $sql = "SELECT a.*,
                   t.nombre AS tipo_asoc
                FROM asociacion a
                INNER JOIN tipo_asoc t 
                    ON a.idTipoAsoc = t.idTipoAsoc
                ORDER BY a.nombre DESC";
            
            // Usamos query directamente para consultas sin parámetros variables
            $consulta = $this->conexion->query($sql);
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        }

        public function listarTipos() {
            $sql = "SELECT * FROM tipo_asoc";
            $consulta = $this->conexion->query($sql);
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        }

        public function obtenerPorId() { 
            $idAsoc = $_GET['idAsoc'];
            // Concatenación directa de la variable en el string
            $sql = "SELECT * FROM asociacion WHERE idAsoc = " . $idAsoc;
            
            $stmt = $this->conexion->query($sql);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function modificar() {
            try {
                $idAsoc = $_GET['idAsoc'];
                $nombre = $_POST['nombre'];
                $fecha_fun = $_POST['fecha_fun'];
                $pista_facil = $_POST['pista_facil'];
                $pista_media = $_POST['pista_media'];
                $pista_dificil = $_POST['pista_dificil'];
                $idTipoAsoc = $_POST['idTipoAsoc'];
                $alcance = $_POST['alcance'];
                $imagen = $_FILES['imagen']['name'];

                // Construcción del SQL mediante concatenación
                $sql = "UPDATE asociacion 
                        SET nombre = '" . $nombre . "',
                            fecha_fun = " . $fecha_fun . ",
                            pista_facil = '" . $pista_facil . "',
                            pista_media = '" . $pista_media . "',
                            pista_dificil = '" . $pista_dificil . "',
                            imagen = '" . $imagen . "',
                            idTipoAsoc = " . $idTipoAsoc . ",
                            alcance = '" . $alcance . "'
                        WHERE idAsoc = " . $idAsoc;

                $this->conexion->query($sql);
                return true;

            } catch (Exception $e) {
                return false;
            }
        }

        public function modificarContribuciones() {
            try {
                $idAsoc = $_GET["idAsoc"];
                
                // Eliminar con concatenación
                $sqlDelete = "DELETE FROM asoc_contribucion WHERE idAsoc = " . $idAsoc;
                $this->conexion->query($sqlDelete);

                if (!empty($_POST['contribuciones'])) {
                    foreach ($_POST['contribuciones'] as $idContribucion) {
                        // Insertar con concatenación
                        $sqlInsert = "INSERT INTO asoc_contribucion (idAsoc, idContribucion)
                                      VALUES (" . $idAsoc . ", " . $idContribucion . ")";
                        $this->conexion->query($sqlInsert);
                    }
                }
                return true;

            } catch (Exception $e) {
                return false;
            }
        }

        public function borrar() {
            try {
                $this->conexion->beginTransaction();
                $idAsoc = $_GET['idAsoc'];

                $sql1 = "DELETE FROM asoc_contribucion WHERE idAsoc = " . $idAsoc;
                $this->conexion->query($sql1);

                $sql2 = "DELETE FROM asociacion WHERE idAsoc = " . $idAsoc;
                $this->conexion->query($sql2);

                $this->conexion->commit();
                return true;

            } catch (Exception $e) {
                $this->conexion->rollBack();
                return false;
            }
        }
    }
?>
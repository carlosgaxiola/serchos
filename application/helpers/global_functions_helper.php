<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists("modulos")) {
	function modulos ($idPerfil) {
		$that =& get_instance();
		$that->load->model("PerfilesModulosModelo");        
		$modulos = $that->PerfilesModulosModelo->modulosPerfil($idPerfil);		
		return $modulos;
	}	
}

if (!function_exists("getModulo")) {
    function getModulo ($nombreModulo) {
        $that =& get_instance();
        $that->load->model("ModulosModelo");
        $modulo = $that->ModulosModelo->buscar(array("nombre" => $nombreModulo));
        return $modulo;
    }
}

if (!function_exists("menu")) {
	function menu ($modulos, $idActual = 1) {    
		if (is_array($modulos)) {
		    foreach ($modulos as $modulo) {                                
                echo "<li class='".(($modulo['id'] == $idActual)? "active": "")."'>";
                echo "  <a href='".base_url($modulo['ruta'])."'>";
                if (strcmp($modulo['fa_icon'], "0") == 0)
                    echo "<i class='no-icon' style='display: none;'>".$modulo['nombre'][0]."</i>";
                else                        
                    echo "      <i class='".$modulo['fa_icon']."' ></i>";
                echo "      <span>".$modulo['nombre']."</span>";
                echo "  </a>";
                echo "</li>";                
            }
        }
	}
}

if (!function_exists("tieneAcceso"))  {
    function tieneAcceso ($idPerfil, $idModulo) {        
        $that =& get_instance();
        $that->load->model("PerfilesModulosModelo");
        return $that->PerfilesModulosModelo->get($idPerfil, $idModulo);
    }
}
if (!function_exists("encriptar_AES")) {    
    function encriptar_AES($string, $key)
    {
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_DEV_URANDOM );
        mcrypt_generic_init($td, $key, $iv);
        $encrypted_data_bin = mcrypt_generic($td, $string);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $encrypted_data_hex = bin2hex($iv).bin2hex($encrypted_data_bin);
        return $encrypted_data_hex;
    }
}
if (!function_exists("desencriptar_AES")) {
    function desencriptar_AES($encrypted_data_hex, $key)
    {
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        $iv_size_hex = mcrypt_enc_get_iv_size($td)*2;
        $iv = pack("H*", substr($encrypted_data_hex, 0, $iv_size_hex));
        $encrypted_data_bin = pack("H*", substr($encrypted_data_hex, $iv_size_hex));
        mcrypt_generic_init($td, $key, $iv);
        $decrypted = mdecrypt_generic($td, $encrypted_data_bin);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $decrypted;
    }
} 
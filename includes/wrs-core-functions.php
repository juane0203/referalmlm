<?php
/**
 * Funciones Core Helpers del Plugin WRS.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Inicia la sesión de PHP si no está activa.
 * El add_action para esta función está en el archivo principal del plugin.
 */
function wrs_start_session() {
    if ( function_exists('session_status') && session_status() === PHP_SESSION_NONE && !headers_sent() ) {
        @session_start(); 
    } elseif ( !session_id() && !headers_sent() ) { // Fallback para versiones PHP < 5.4
         @session_start();
    }
}

/**
 * Devuelve los datos geográficos de Colombia (Departamentos y Ciudades).
 * @return array
 */
function wrs_get_colombia_geo_data() {
    // PEGA AQUÍ TU ARRAY COMPLETO de wrs_get_colombia_geo_data()
    // que ya me confirmaste que tienes.
    // Ejemplo (DEBES USAR EL TUYO COMPLETO):
  return array(
    "ANTIOQUIA" => array(
      "abbr" => "ANT",
      "code" => "05",
      "name" => "ANTIOQUIA",
      "cities" => array(
        "MEDELLÍN" => array(
          "code" => "05001",
          "name" => "MEDELLÍN",
        ),
        "ABEJORRAL" => array(
          "code" => "05002",
          "name" => "ABEJORRAL",
        ),
        "ABRIAQUÍ" => array(
          "code" => "05004",
          "name" => "ABRIAQUÍ",
        ),
        "ALEJANDRÍA" => array(
          "code" => "05021",
          "name" => "ALEJANDRÍA",
        ),
        "AMAGÁ" => array(
          "code" => "05030",
          "name" => "AMAGÁ",
        ),
        "AMALFI" => array(
          "code" => "05031",
          "name" => "AMALFI",
        ),
        "ANDES" => array(
          "code" => "05034",
          "name" => "ANDES",
        ),
        "ANGELÓPOLIS" => array(
          "code" => "05036",
          "name" => "ANGELÓPOLIS",
        ),
        "ANGOSTURA" => array(
          "code" => "05038",
          "name" => "ANGOSTURA",
        ),
        "ANORÍ" => array(
          "code" => "05040",
          "name" => "ANORÍ",
        ),
        "SANTA FÉ DE ANTIOQUIA" => array(
          "code" => "05042",
          "name" => "SANTA FÉ DE ANTIOQUIA",
        ),
        "ANZÁ" => array(
          "code" => "05044",
          "name" => "ANZÁ",
        ),
        "APARTADÓ" => array(
          "code" => "05045",
          "name" => "APARTADÓ",
        ),
        "ARBOLETES" => array(
          "code" => "05051",
          "name" => "ARBOLETES",
        ),
        "ARGELIA" => array(
          "code" => "05055",
          "name" => "ARGELIA",
        ),
        "ARMENIA" => array(
          "code" => "05059",
          "name" => "ARMENIA",
        ),
        "BARBOSA" => array(
          "code" => "05079",
          "name" => "BARBOSA",
        ),
        "BELMIRA" => array(
          "code" => "05086",
          "name" => "BELMIRA",
        ),
        "BELLO" => array(
          "code" => "05088",
          "name" => "BELLO",
        ),
        "BETANIA" => array(
          "code" => "05091",
          "name" => "BETANIA",
        ),
        "BETULIA" => array(
          "code" => "05093",
          "name" => "BETULIA",
        ),
        "CIUDAD BOLÍVAR" => array(
          "code" => "05101",
          "name" => "CIUDAD BOLÍVAR",
        ),
        "BRICEÑO" => array(
          "code" => "05107",
          "name" => "BRICEÑO",
        ),
        "BURITICÁ" => array(
          "code" => "05113",
          "name" => "BURITICÁ",
        ),
        "CÁCERES" => array(
          "code" => "05120",
          "name" => "CÁCERES",
        ),
        "CAICEDO" => array(
          "code" => "05125",
          "name" => "CAICEDO",
        ),
        "CALDAS" => array(
          "code" => "05129",
          "name" => "CALDAS",
        ),
        "CAMPAMENTO" => array(
          "code" => "05134",
          "name" => "CAMPAMENTO",
        ),
        "CAÑASGORDAS" => array(
          "code" => "05138",
          "name" => "CAÑASGORDAS",
        ),
        "CARACOLÍ" => array(
          "code" => "05142",
          "name" => "CARACOLÍ",
        ),
        "CARAMANTA" => array(
          "code" => "05145",
          "name" => "CARAMANTA",
        ),
        "CAREPA" => array(
          "code" => "05147",
          "name" => "CAREPA",
        ),
        "EL CARMEN DE VIBORAL" => array(
          "code" => "05148",
          "name" => "EL CARMEN DE VIBORAL",
        ),
        "CAROLINA" => array(
          "code" => "05150",
          "name" => "CAROLINA",
        ),
        "CAUCASIA" => array(
          "code" => "05154",
          "name" => "CAUCASIA",
        ),
        "CHIGORODÓ" => array(
          "code" => "05172",
          "name" => "CHIGORODÓ",
        ),
        "CISNEROS" => array(
          "code" => "05190",
          "name" => "CISNEROS",
        ),
        "COCORNÁ" => array(
          "code" => "05197",
          "name" => "COCORNÁ",
        ),
        "CONCEPCIÓN" => array(
          "code" => "05206",
          "name" => "CONCEPCIÓN",
        ),
        "CONCORDIA" => array(
          "code" => "05209",
          "name" => "CONCORDIA",
        ),
        "COPACABANA" => array(
          "code" => "05212",
          "name" => "COPACABANA",
        ),
        "DABEIBA" => array(
          "code" => "05234",
          "name" => "DABEIBA",
        ),
        "DONMATÍAS" => array(
          "code" => "05237",
          "name" => "DONMATÍAS",
        ),
        "EBÉJICO" => array(
          "code" => "05240",
          "name" => "EBÉJICO",
        ),
        "EL BAGRE" => array(
          "code" => "05250",
          "name" => "EL BAGRE",
        ),
        "ENTRERRÍOS" => array(
          "code" => "05264",
          "name" => "ENTRERRÍOS",
        ),
        "ENVIGADO" => array(
          "code" => "05266",
          "name" => "ENVIGADO",
        ),
        "FREDONIA" => array(
          "code" => "05282",
          "name" => "FREDONIA",
        ),
        "FRONTINO" => array(
          "code" => "05284",
          "name" => "FRONTINO",
        ),
        "GIRALDO" => array(
          "code" => "05306",
          "name" => "GIRALDO",
        ),
        "GIRARDOTA" => array(
          "code" => "05308",
          "name" => "GIRARDOTA",
        ),
        "GÓMEZ PLATA" => array(
          "code" => "05310",
          "name" => "GÓMEZ PLATA",
        ),
        "GRANADA" => array(
          "code" => "05313",
          "name" => "GRANADA",
        ),
        "GUADALUPE" => array(
          "code" => "05315",
          "name" => "GUADALUPE",
        ),
        "GUARNE" => array(
          "code" => "05318",
          "name" => "GUARNE",
        ),
        "GUATAPÉ" => array(
          "code" => "05321",
          "name" => "GUATAPÉ",
        ),
        "HELICONIA" => array(
          "code" => "05347",
          "name" => "HELICONIA",
        ),
        "HISPANIA" => array(
          "code" => "05353",
          "name" => "HISPANIA",
        ),
        "ITAGÜÍ" => array(
          "code" => "05360",
          "name" => "ITAGÜÍ",
        ),
        "ITUANGO" => array(
          "code" => "05361",
          "name" => "ITUANGO",
        ),
        "JARDÍN" => array(
          "code" => "05364",
          "name" => "JARDÍN",
        ),
        "JERICÓ" => array(
          "code" => "05368",
          "name" => "JERICÓ",
        ),
        "LA CEJA" => array(
          "code" => "05376",
          "name" => "LA CEJA",
        ),
        "LA ESTRELLA" => array(
          "code" => "05380",
          "name" => "LA ESTRELLA",
        ),
        "LA PINTADA" => array(
          "code" => "05390",
          "name" => "LA PINTADA",
        ),
        "LA UNIÓN" => array(
          "code" => "05400",
          "name" => "LA UNIÓN",
        ),
        "LIBORINA" => array(
          "code" => "05411",
          "name" => "LIBORINA",
        ),
        "MACEO" => array(
          "code" => "05425",
          "name" => "MACEO",
        ),
        "MARINILLA" => array(
          "code" => "05440",
          "name" => "MARINILLA",
        ),
        "MONTEBELLO" => array(
          "code" => "05467",
          "name" => "MONTEBELLO",
        ),
        "MURINDÓ" => array(
          "code" => "05475",
          "name" => "MURINDÓ",
        ),
        "MUTATÁ" => array(
          "code" => "05480",
          "name" => "MUTATÁ",
        ),
        "NARIÑO" => array(
          "code" => "05483",
          "name" => "NARIÑO",
        ),
        "NECOCLÍ" => array(
          "code" => "05490",
          "name" => "NECOCLÍ",
        ),
        "NECHÍ" => array(
          "code" => "05495",
          "name" => "NECHÍ",
        ),
        "OLAYA" => array(
          "code" => "05501",
          "name" => "OLAYA",
        ),
        "PEÑOL" => array(
          "code" => "05541",
          "name" => "PEÑOL",
        ),
        "PEQUE" => array(
          "code" => "05543",
          "name" => "PEQUE",
        ),
        "PUEBLORRICO" => array(
          "code" => "05576",
          "name" => "PUEBLORRICO",
        ),
        "PUERTO BERRÍO" => array(
          "code" => "05579",
          "name" => "PUERTO BERRÍO",
        ),
        "PUERTO NARE" => array(
          "code" => "05585",
          "name" => "PUERTO NARE",
        ),
        "PUERTO TRIUNFO" => array(
          "code" => "05591",
          "name" => "PUERTO TRIUNFO",
        ),
        "REMEDIOS" => array(
          "code" => "05604",
          "name" => "REMEDIOS",
        ),
        "RETIRO" => array(
          "code" => "05607",
          "name" => "RETIRO",
        ),
        "RIONEGRO" => array(
          "code" => "05615",
          "name" => "RIONEGRO",
        ),
        "SABANALARGA" => array(
          "code" => "05628",
          "name" => "SABANALARGA",
        ),
        "SABANETA" => array(
          "code" => "05631",
          "name" => "SABANETA",
        ),
        "SALGAR" => array(
          "code" => "05642",
          "name" => "SALGAR",
        ),
        "SAN ANDRÉS DE CUERQUÍA" => array(
          "code" => "05647",
          "name" => "SAN ANDRÉS DE CUERQUÍA",
        ),
        "SAN CARLOS" => array(
          "code" => "05649",
          "name" => "SAN CARLOS",
        ),
        "SAN FRANCISCO" => array(
          "code" => "05652",
          "name" => "SAN FRANCISCO",
        ),
        "SAN JERÓNIMO" => array(
          "code" => "05656",
          "name" => "SAN JERÓNIMO",
        ),
        "SAN JOSÉ DE LA MONTAÑA" => array(
          "code" => "05658",
          "name" => "SAN JOSÉ DE LA MONTAÑA",
        ),
        "SAN JUAN DE URABÁ" => array(
          "code" => "05659",
          "name" => "SAN JUAN DE URABÁ",
        ),
        "SAN LUIS" => array(
          "code" => "05660",
          "name" => "SAN LUIS",
        ),
        "SAN PEDRO DE LOS MILAGROS" => array(
          "code" => "05664",
          "name" => "SAN PEDRO DE LOS MILAGROS",
        ),
        "SAN PEDRO DE URABÁ" => array(
          "code" => "05665",
          "name" => "SAN PEDRO DE URABÁ",
        ),
        "SAN RAFAEL" => array(
          "code" => "05667",
          "name" => "SAN RAFAEL",
        ),
        "SAN ROQUE" => array(
          "code" => "05670",
          "name" => "SAN ROQUE",
        ),
        "SAN VICENTE FERRER" => array(
          "code" => "05674",
          "name" => "SAN VICENTE FERRER",
        ),
        "SANTA BÁRBARA" => array(
          "code" => "05679",
          "name" => "SANTA BÁRBARA",
        ),
        "SANTA ROSA DE OSOS" => array(
          "code" => "05686",
          "name" => "SANTA ROSA DE OSOS",
        ),
        "SANTO DOMINGO" => array(
          "code" => "05690",
          "name" => "SANTO DOMINGO",
        ),
        "EL SANTUARIO" => array(
          "code" => "05697",
          "name" => "EL SANTUARIO",
        ),
        "SEGOVIA" => array(
          "code" => "05736",
          "name" => "SEGOVIA",
        ),
        "SONSÓN" => array(
          "code" => "05756",
          "name" => "SONSÓN",
        ),
        "SOPETRÁN" => array(
          "code" => "05761",
          "name" => "SOPETRÁN",
        ),
        "TÁMESIS" => array(
          "code" => "05789",
          "name" => "TÁMESIS",
        ),
        "TARAZÁ" => array(
          "code" => "05790",
          "name" => "TARAZÁ",
        ),
        "TARSO" => array(
          "code" => "05792",
          "name" => "TARSO",
        ),
        "TITIRIBÍ" => array(
          "code" => "05809",
          "name" => "TITIRIBÍ",
        ),
        "TOLEDO" => array(
          "code" => "05819",
          "name" => "TOLEDO",
        ),
        "TURBO" => array(
          "code" => "05837",
          "name" => "TURBO",
        ),
        "URAMITA" => array(
          "code" => "05842",
          "name" => "URAMITA",
        ),
        "URRAO" => array(
          "code" => "05847",
          "name" => "URRAO",
        ),
        "VALDIVIA" => array(
          "code" => "05854",
          "name" => "VALDIVIA",
        ),
        "VALPARAÍSO" => array(
          "code" => "05856",
          "name" => "VALPARAÍSO",
        ),
        "VEGACHÍ" => array(
          "code" => "05858",
          "name" => "VEGACHÍ",
        ),
        "VENECIA" => array(
          "code" => "05861",
          "name" => "VENECIA",
        ),
        "VIGÍA DEL FUERTE" => array(
          "code" => "05873",
          "name" => "VIGÍA DEL FUERTE",
        ),
        "YALÍ" => array(
          "code" => "05885",
          "name" => "YALÍ",
        ),
        "YARUMAL" => array(
          "code" => "05887",
          "name" => "YARUMAL",
        ),
        "YOLOMBÓ" => array(
          "code" => "05890",
          "name" => "YOLOMBÓ",
        ),
        "YONDÓ" => array(
          "code" => "05893",
          "name" => "YONDÓ",
        ),
        "ZARAGOZA" => array(
          "code" => "05895",
          "name" => "ZARAGOZA",
        ),
      ),
    ),
    "ATLÁNTICO" => array(
      "abbr" => "ATL",
      "code" => "08",
      "name" => "ATLÁNTICO",
      "cities" => array(
        "BARRANQUILLA" => array(
          "code" => "08001",
          "name" => "BARRANQUILLA",
        ),
        "BARANOA" => array(
          "code" => "08078",
          "name" => "BARANOA",
        ),
        "CAMPO DE LA CRUZ" => array(
          "code" => "08137",
          "name" => "CAMPO DE LA CRUZ",
        ),
        "CANDELARIA" => array(
          "code" => "08141",
          "name" => "CANDELARIA",
        ),
        "GALAPA" => array(
          "code" => "08296",
          "name" => "GALAPA",
        ),
        "JUAN DE ACOSTA" => array(
          "code" => "08372",
          "name" => "JUAN DE ACOSTA",
        ),
        "LURUACO" => array(
          "code" => "08421",
          "name" => "LURUACO",
        ),
        "MALAMBO" => array(
          "code" => "08433",
          "name" => "MALAMBO",
        ),
        "MANATÍ" => array(
          "code" => "08436",
          "name" => "MANATÍ",
        ),
        "PALMAR DE VARELA" => array(
          "code" => "08520",
          "name" => "PALMAR DE VARELA",
        ),
        "PIOJÓ" => array(
          "code" => "08549",
          "name" => "PIOJÓ",
        ),
        "POLONUEVO" => array(
          "code" => "08558",
          "name" => "POLONUEVO",
        ),
        "PONEDERA" => array(
          "code" => "08560",
          "name" => "PONEDERA",
        ),
        "PUERTO COLOMBIA" => array(
          "code" => "08573",
          "name" => "PUERTO COLOMBIA",
        ),
        "REPELÓN" => array(
          "code" => "08606",
          "name" => "REPELÓN",
        ),
        "SABANAGRANDE" => array(
          "code" => "08634",
          "name" => "SABANAGRANDE",
        ),
        "SABANALARGA" => array(
          "code" => "08638",
          "name" => "SABANALARGA",
        ),
        "SANTA LUCÍA" => array(
          "code" => "08675",
          "name" => "SANTA LUCÍA",
        ),
        "SANTO TOMÁS" => array(
          "code" => "08685",
          "name" => "SANTO TOMÁS",
        ),
        "SOLEDAD" => array(
          "code" => "08758",
          "name" => "SOLEDAD",
        ),
        "SUAN" => array(
          "code" => "08770",
          "name" => "SUAN",
        ),
        "TUBARÁ" => array(
          "code" => "08832",
          "name" => "TUBARÁ",
        ),
        "USIACURÍ" => array(
          "code" => "08849",
          "name" => "USIACURÍ",
        ),
      ),
    ),
    "BOGOTA_DC" => array(
      "abbr" => "BOG",
      "code" => "11",
      "name" => "Bogot&aacute; D.C.",
      "cities" => array(
        "BOGOTA_DC" => array(
          "code" => "11001",
          "name" => "Bogot&aacute; D.C.",
        ),
      ),
    ),
    "BOLÍVAR" => array(
      "abbr" => "BOL",
      "code" => "13",
      "name" => "BOLÍVAR",
      "cities" => array(
        "CARTAGENA DE INDIAS" => array(
          "code" => "13001",
          "name" => "CARTAGENA DE INDIAS",
        ),
        "ACHÍ" => array(
          "code" => "13006",
          "name" => "ACHÍ",
        ),
        "ALTOS DEL ROSARIO" => array(
          "code" => "13030",
          "name" => "ALTOS DEL ROSARIO",
        ),
        "ARENAL" => array(
          "code" => "13042",
          "name" => "ARENAL",
        ),
        "ARJONA" => array(
          "code" => "13052",
          "name" => "ARJONA",
        ),
        "ARROYOHONDO" => array(
          "code" => "13062",
          "name" => "ARROYOHONDO",
        ),
        "BARRANCO DE LOBA" => array(
          "code" => "13074",
          "name" => "BARRANCO DE LOBA",
        ),
        "CALAMAR" => array(
          "code" => "13140",
          "name" => "CALAMAR",
        ),
        "CANTAGALLO" => array(
          "code" => "13160",
          "name" => "CANTAGALLO",
        ),
        "CICUCO" => array(
          "code" => "13188",
          "name" => "CICUCO",
        ),
        "CÓRDOBA" => array(
          "code" => "13212",
          "name" => "CÓRDOBA",
        ),
        "CLEMENCIA" => array(
          "code" => "13222",
          "name" => "CLEMENCIA",
        ),
        "EL CARMEN DE BOLÍVAR" => array(
          "code" => "13244",
          "name" => "EL CARMEN DE BOLÍVAR",
        ),
        "EL GUAMO" => array(
          "code" => "13248",
          "name" => "EL GUAMO",
        ),
        "EL PEÑÓN" => array(
          "code" => "13268",
          "name" => "EL PEÑÓN",
        ),
        "HATILLO DE LOBA" => array(
          "code" => "13300",
          "name" => "HATILLO DE LOBA",
        ),
        "MAGANGUÉ" => array(
          "code" => "13430",
          "name" => "MAGANGUÉ",
        ),
        "MAHATES" => array(
          "code" => "13433",
          "name" => "MAHATES",
        ),
        "MARGARITA" => array(
          "code" => "13440",
          "name" => "MARGARITA",
        ),
        "MARÍA LA BAJA" => array(
          "code" => "13442",
          "name" => "MARÍA LA BAJA",
        ),
        "MONTECRISTO" => array(
          "code" => "13458",
          "name" => "MONTECRISTO",
        ),
        "SANTA CRUZ DE MOMPOX" => array(
          "code" => "13468",
          "name" => "SANTA CRUZ DE MOMPOX",
        ),
        "MORALES" => array(
          "code" => "13473",
          "name" => "MORALES",
        ),
        "NOROSÍ" => array(
          "code" => "13490",
          "name" => "NOROSÍ",
        ),
        "PINILLOS" => array(
          "code" => "13549",
          "name" => "PINILLOS",
        ),
        "REGIDOR" => array(
          "code" => "13580",
          "name" => "REGIDOR",
        ),
        "RÍO VIEJO" => array(
          "code" => "13600",
          "name" => "RÍO VIEJO",
        ),
        "SAN CRISTÓBAL" => array(
          "code" => "13620",
          "name" => "SAN CRISTÓBAL",
        ),
        "SAN ESTANISLAO" => array(
          "code" => "13647",
          "name" => "SAN ESTANISLAO",
        ),
        "SAN FERNANDO" => array(
          "code" => "13650",
          "name" => "SAN FERNANDO",
        ),
        "SAN JACINTO" => array(
          "code" => "13654",
          "name" => "SAN JACINTO",
        ),
        "SAN JACINTO DEL CAUCA" => array(
          "code" => "13655",
          "name" => "SAN JACINTO DEL CAUCA",
        ),
        "SAN JUAN NEPOMUCENO" => array(
          "code" => "13657",
          "name" => "SAN JUAN NEPOMUCENO",
        ),
        "SAN MARTÍN DE LOBA" => array(
          "code" => "13667",
          "name" => "SAN MARTÍN DE LOBA",
        ),
        "SAN PABLO" => array(
          "code" => "13670",
          "name" => "SAN PABLO",
        ),
        "SANTA CATALINA" => array(
          "code" => "13673",
          "name" => "SANTA CATALINA",
        ),
        "SANTA ROSA" => array(
          "code" => "13683",
          "name" => "SANTA ROSA",
        ),
        "SANTA ROSA DEL SUR" => array(
          "code" => "13688",
          "name" => "SANTA ROSA DEL SUR",
        ),
        "SIMITÍ" => array(
          "code" => "13744",
          "name" => "SIMITÍ",
        ),
        "SOPLAVIENTO" => array(
          "code" => "13760",
          "name" => "SOPLAVIENTO",
        ),
        "TALAIGUA NUEVO" => array(
          "code" => "13780",
          "name" => "TALAIGUA NUEVO",
        ),
        "TIQUISIO" => array(
          "code" => "13810",
          "name" => "TIQUISIO",
        ),
        "TURBACO" => array(
          "code" => "13836",
          "name" => "TURBACO",
        ),
        "TURBANÁ" => array(
          "code" => "13838",
          "name" => "TURBANÁ",
        ),
        "VILLANUEVA" => array(
          "code" => "13873",
          "name" => "VILLANUEVA",
        ),
        "ZAMBRANO" => array(
          "code" => "13894",
          "name" => "ZAMBRANO",
        ),
      ),
    ),
    "BOYACÁ" => array(
      "abbr" => "BOY",
      "code" => "15",
      "name" => "BOYACÁ",
      "cities" => array(
        "TUNJA" => array(
          "code" => "15001",
          "name" => "TUNJA",
        ),
        "ALMEIDA" => array(
          "code" => "15022",
          "name" => "ALMEIDA",
        ),
        "AQUITANIA" => array(
          "code" => "15047",
          "name" => "AQUITANIA",
        ),
        "ARCABUCO" => array(
          "code" => "15051",
          "name" => "ARCABUCO",
        ),
        "BELÉN" => array(
          "code" => "15087",
          "name" => "BELÉN",
        ),
        "BERBEO" => array(
          "code" => "15090",
          "name" => "BERBEO",
        ),
        "BETÉITIVA" => array(
          "code" => "15092",
          "name" => "BETÉITIVA",
        ),
        "BOAVITA" => array(
          "code" => "15097",
          "name" => "BOAVITA",
        ),
        "BOYACÁ" => array(
          "code" => "15104",
          "name" => "BOYACÁ",
        ),
        "BRICEÑO" => array(
          "code" => "15106",
          "name" => "BRICEÑO",
        ),
        "BUENAVISTA" => array(
          "code" => "15109",
          "name" => "BUENAVISTA",
        ),
        "BUSBANZÁ" => array(
          "code" => "15114",
          "name" => "BUSBANZÁ",
        ),
        "CALDAS" => array(
          "code" => "15131",
          "name" => "CALDAS",
        ),
        "CAMPOHERMOSO" => array(
          "code" => "15135",
          "name" => "CAMPOHERMOSO",
        ),
        "CERINZA" => array(
          "code" => "15162",
          "name" => "CERINZA",
        ),
        "CHINAVITA" => array(
          "code" => "15172",
          "name" => "CHINAVITA",
        ),
        "CHIQUINQUIRÁ" => array(
          "code" => "15176",
          "name" => "CHIQUINQUIRÁ",
        ),
        "CHISCAS" => array(
          "code" => "15180",
          "name" => "CHISCAS",
        ),
        "CHITA" => array(
          "code" => "15183",
          "name" => "CHITA",
        ),
        "CHITARAQUE" => array(
          "code" => "15185",
          "name" => "CHITARAQUE",
        ),
        "CHIVATÁ" => array(
          "code" => "15187",
          "name" => "CHIVATÁ",
        ),
        "CIÉNEGA" => array(
          "code" => "15189",
          "name" => "CIÉNEGA",
        ),
        "CÓMBITA" => array(
          "code" => "15204",
          "name" => "CÓMBITA",
        ),
        "COPER" => array(
          "code" => "15212",
          "name" => "COPER",
        ),
        "CORRALES" => array(
          "code" => "15215",
          "name" => "CORRALES",
        ),
        "COVARACHÍA" => array(
          "code" => "15218",
          "name" => "COVARACHÍA",
        ),
        "CUBARÁ" => array(
          "code" => "15223",
          "name" => "CUBARÁ",
        ),
        "CUCAITA" => array(
          "code" => "15224",
          "name" => "CUCAITA",
        ),
        "CUÍTIVA" => array(
          "code" => "15226",
          "name" => "CUÍTIVA",
        ),
        "CHÍQUIZA" => array(
          "code" => "15232",
          "name" => "CHÍQUIZA",
        ),
        "CHIVOR" => array(
          "code" => "15236",
          "name" => "CHIVOR",
        ),
        "DUITAMA" => array(
          "code" => "15238",
          "name" => "DUITAMA",
        ),
        "EL COCUY" => array(
          "code" => "15244",
          "name" => "EL COCUY",
        ),
        "EL ESPINO" => array(
          "code" => "15248",
          "name" => "EL ESPINO",
        ),
        "FIRAVITOBA" => array(
          "code" => "15272",
          "name" => "FIRAVITOBA",
        ),
        "FLORESTA" => array(
          "code" => "15276",
          "name" => "FLORESTA",
        ),
        "GACHANTIVÁ" => array(
          "code" => "15293",
          "name" => "GACHANTIVÁ",
        ),
        "GÁMEZA" => array(
          "code" => "15296",
          "name" => "GÁMEZA",
        ),
        "GARAGOA" => array(
          "code" => "15299",
          "name" => "GARAGOA",
        ),
        "GUACAMAYAS" => array(
          "code" => "15317",
          "name" => "GUACAMAYAS",
        ),
        "GUATEQUE" => array(
          "code" => "15322",
          "name" => "GUATEQUE",
        ),
        "GUAYATÁ" => array(
          "code" => "15325",
          "name" => "GUAYATÁ",
        ),
        "GÜICÁN DE LA SIERRA" => array(
          "code" => "15332",
          "name" => "GÜICÁN DE LA SIERRA",
        ),
        "IZA" => array(
          "code" => "15362",
          "name" => "IZA",
        ),
        "JENESANO" => array(
          "code" => "15367",
          "name" => "JENESANO",
        ),
        "JERICÓ" => array(
          "code" => "15368",
          "name" => "JERICÓ",
        ),
        "LABRANZAGRANDE" => array(
          "code" => "15377",
          "name" => "LABRANZAGRANDE",
        ),
        "LA CAPILLA" => array(
          "code" => "15380",
          "name" => "LA CAPILLA",
        ),
        "LA VICTORIA" => array(
          "code" => "15401",
          "name" => "LA VICTORIA",
        ),
        "LA UVITA" => array(
          "code" => "15403",
          "name" => "LA UVITA",
        ),
        "VILLA DE LEYVA" => array(
          "code" => "15407",
          "name" => "VILLA DE LEYVA",
        ),
        "MACANAL" => array(
          "code" => "15425",
          "name" => "MACANAL",
        ),
        "MARIPÍ" => array(
          "code" => "15442",
          "name" => "MARIPÍ",
        ),
        "MIRAFLORES" => array(
          "code" => "15455",
          "name" => "MIRAFLORES",
        ),
        "MONGUA" => array(
          "code" => "15464",
          "name" => "MONGUA",
        ),
        "MONGUÍ" => array(
          "code" => "15466",
          "name" => "MONGUÍ",
        ),
        "MONIQUIRÁ" => array(
          "code" => "15469",
          "name" => "MONIQUIRÁ",
        ),
        "MOTAVITA" => array(
          "code" => "15476",
          "name" => "MOTAVITA",
        ),
        "MUZO" => array(
          "code" => "15480",
          "name" => "MUZO",
        ),
        "NOBSA" => array(
          "code" => "15491",
          "name" => "NOBSA",
        ),
        "NUEVO COLÓN" => array(
          "code" => "15494",
          "name" => "NUEVO COLÓN",
        ),
        "OICATÁ" => array(
          "code" => "15500",
          "name" => "OICATÁ",
        ),
        "OTANCHE" => array(
          "code" => "15507",
          "name" => "OTANCHE",
        ),
        "PACHAVITA" => array(
          "code" => "15511",
          "name" => "PACHAVITA",
        ),
        "PÁEZ" => array(
          "code" => "15514",
          "name" => "PÁEZ",
        ),
        "PAIPA" => array(
          "code" => "15516",
          "name" => "PAIPA",
        ),
        "PAJARITO" => array(
          "code" => "15518",
          "name" => "PAJARITO",
        ),
        "PANQUEBA" => array(
          "code" => "15522",
          "name" => "PANQUEBA",
        ),
        "PAUNA" => array(
          "code" => "15531",
          "name" => "PAUNA",
        ),
        "PAYA" => array(
          "code" => "15533",
          "name" => "PAYA",
        ),
        "PAZ DE RÍO" => array(
          "code" => "15537",
          "name" => "PAZ DE RÍO",
        ),
        "PESCA" => array(
          "code" => "15542",
          "name" => "PESCA",
        ),
        "PISBA" => array(
          "code" => "15550",
          "name" => "PISBA",
        ),
        "PUERTO BOYACÁ" => array(
          "code" => "15572",
          "name" => "PUERTO BOYACÁ",
        ),
        "QUÍPAMA" => array(
          "code" => "15580",
          "name" => "QUÍPAMA",
        ),
        "RAMIRIQUÍ" => array(
          "code" => "15599",
          "name" => "RAMIRIQUÍ",
        ),
        "RÁQUIRA" => array(
          "code" => "15600",
          "name" => "RÁQUIRA",
        ),
        "RONDÓN" => array(
          "code" => "15621",
          "name" => "RONDÓN",
        ),
        "SABOYÁ" => array(
          "code" => "15632",
          "name" => "SABOYÁ",
        ),
        "SÁCHICA" => array(
          "code" => "15638",
          "name" => "SÁCHICA",
        ),
        "SAMACÁ" => array(
          "code" => "15646",
          "name" => "SAMACÁ",
        ),
        "SAN EDUARDO" => array(
          "code" => "15660",
          "name" => "SAN EDUARDO",
        ),
        "SAN JOSÉ DE PARE" => array(
          "code" => "15664",
          "name" => "SAN JOSÉ DE PARE",
        ),
        "SAN LUIS DE GACENO" => array(
          "code" => "15667",
          "name" => "SAN LUIS DE GACENO",
        ),
        "SAN MATEO" => array(
          "code" => "15673",
          "name" => "SAN MATEO",
        ),
        "SAN MIGUEL DE SEMA" => array(
          "code" => "15676",
          "name" => "SAN MIGUEL DE SEMA",
        ),
        "SAN PABLO DE BORBUR" => array(
          "code" => "15681",
          "name" => "SAN PABLO DE BORBUR",
        ),
        "SANTANA" => array(
          "code" => "15686",
          "name" => "SANTANA",
        ),
        "SANTA MARÍA" => array(
          "code" => "15690",
          "name" => "SANTA MARÍA",
        ),
        "SANTA ROSA DE VITERBO" => array(
          "code" => "15693",
          "name" => "SANTA ROSA DE VITERBO",
        ),
        "SANTA SOFÍA" => array(
          "code" => "15696",
          "name" => "SANTA SOFÍA",
        ),
        "SATIVANORTE" => array(
          "code" => "15720",
          "name" => "SATIVANORTE",
        ),
        "SATIVASUR" => array(
          "code" => "15723",
          "name" => "SATIVASUR",
        ),
        "SIACHOQUE" => array(
          "code" => "15740",
          "name" => "SIACHOQUE",
        ),
        "SOATÁ" => array(
          "code" => "15753",
          "name" => "SOATÁ",
        ),
        "SOCOTÁ" => array(
          "code" => "15755",
          "name" => "SOCOTÁ",
        ),
        "SOCHA" => array(
          "code" => "15757",
          "name" => "SOCHA",
        ),
        "SOGAMOSO" => array(
          "code" => "15759",
          "name" => "SOGAMOSO",
        ),
        "SOMONDOCO" => array(
          "code" => "15761",
          "name" => "SOMONDOCO",
        ),
        "SORA" => array(
          "code" => "15762",
          "name" => "SORA",
        ),
        "SOTAQUIRÁ" => array(
          "code" => "15763",
          "name" => "SOTAQUIRÁ",
        ),
        "SORACÁ" => array(
          "code" => "15764",
          "name" => "SORACÁ",
        ),
        "SUSACÓN" => array(
          "code" => "15774",
          "name" => "SUSACÓN",
        ),
        "SUTAMARCHÁN" => array(
          "code" => "15776",
          "name" => "SUTAMARCHÁN",
        ),
        "SUTATENZA" => array(
          "code" => "15778",
          "name" => "SUTATENZA",
        ),
        "TASCO" => array(
          "code" => "15790",
          "name" => "TASCO",
        ),
        "TENZA" => array(
          "code" => "15798",
          "name" => "TENZA",
        ),
        "TIBANÁ" => array(
          "code" => "15804",
          "name" => "TIBANÁ",
        ),
        "TIBASOSA" => array(
          "code" => "15806",
          "name" => "TIBASOSA",
        ),
        "TINJACÁ" => array(
          "code" => "15808",
          "name" => "TINJACÁ",
        ),
        "TIPACOQUE" => array(
          "code" => "15810",
          "name" => "TIPACOQUE",
        ),
        "TOCA" => array(
          "code" => "15814",
          "name" => "TOCA",
        ),
        "TOGÜÍ" => array(
          "code" => "15816",
          "name" => "TOGÜÍ",
        ),
        "TÓPAGA" => array(
          "code" => "15820",
          "name" => "TÓPAGA",
        ),
        "TOTA" => array(
          "code" => "15822",
          "name" => "TOTA",
        ),
        "TUNUNGUÁ" => array(
          "code" => "15832",
          "name" => "TUNUNGUÁ",
        ),
        "TURMEQUÉ" => array(
          "code" => "15835",
          "name" => "TURMEQUÉ",
        ),
        "TUTA" => array(
          "code" => "15837",
          "name" => "TUTA",
        ),
        "TUTAZÁ" => array(
          "code" => "15839",
          "name" => "TUTAZÁ",
        ),
        "ÚMBITA" => array(
          "code" => "15842",
          "name" => "ÚMBITA",
        ),
        "VENTAQUEMADA" => array(
          "code" => "15861",
          "name" => "VENTAQUEMADA",
        ),
        "VIRACACHÁ" => array(
          "code" => "15879",
          "name" => "VIRACACHÁ",
        ),
        "ZETAQUIRA" => array(
          "code" => "15897",
          "name" => "ZETAQUIRA",
        ),
      ),
    ),
    "CAUCA" => array(
      "abbr" => "CAU",
      "code" => "19",
      "name" => "CAUCA",
      "cities" => array(
        "GUAPI" => array(
          "code" => "19318",
          "name" => "GUAPI",
        ),
        "POPAYÁN" => array(
          "code" => "19001",
          "name" => "POPAYÁN",
        ),
        "ALMAGUER" => array(
          "code" => "19022",
          "name" => "ALMAGUER",
        ),
        "ARGELIA" => array(
          "code" => "19050",
          "name" => "ARGELIA",
        ),
        "BALBOA" => array(
          "code" => "19075",
          "name" => "BALBOA",
        ),
        "BOLÍVAR" => array(
          "code" => "19100",
          "name" => "BOLÍVAR",
        ),
        "BUENOS AIRES" => array(
          "code" => "19110",
          "name" => "BUENOS AIRES",
        ),
        "CAJIBÍO" => array(
          "code" => "19130",
          "name" => "CAJIBÍO",
        ),
        "CALDONO" => array(
          "code" => "19137",
          "name" => "CALDONO",
        ),
        "CALOTO" => array(
          "code" => "19142",
          "name" => "CALOTO",
        ),
        "CORINTO" => array(
          "code" => "19212",
          "name" => "CORINTO",
        ),
        "EL TAMBO" => array(
          "code" => "19256",
          "name" => "EL TAMBO",
        ),
        "FLORENCIA" => array(
          "code" => "19290",
          "name" => "FLORENCIA",
        ),
        "GUACHENÉ" => array(
          "code" => "19300",
          "name" => "GUACHENÉ",
        ),
        "INZÁ" => array(
          "code" => "19355",
          "name" => "INZÁ",
        ),
        "JAMBALÓ" => array(
          "code" => "19364",
          "name" => "JAMBALÓ",
        ),
        "LA SIERRA" => array(
          "code" => "19392",
          "name" => "LA SIERRA",
        ),
        "LA VEGA" => array(
          "code" => "19397",
          "name" => "LA VEGA",
        ),
        "LÓPEZ DE MICAY" => array(
          "code" => "19418",
          "name" => "LÓPEZ DE MICAY",
        ),
        "MERCADERES" => array(
          "code" => "19450",
          "name" => "MERCADERES",
        ),
        "MIRANDA" => array(
          "code" => "19455",
          "name" => "MIRANDA",
        ),
        "MORALES" => array(
          "code" => "19473",
          "name" => "MORALES",
        ),
        "PADILLA" => array(
          "code" => "19513",
          "name" => "PADILLA",
        ),
        "PÁEZ" => array(
          "code" => "19517",
          "name" => "PÁEZ",
        ),
        "PATÍA" => array(
          "code" => "19532",
          "name" => "PATÍA",
        ),
        "PIAMONTE" => array(
          "code" => "19533",
          "name" => "PIAMONTE",
        ),
        "PIENDAMÓ - TUNÍA" => array(
          "code" => "19548",
          "name" => "PIENDAMÓ - TUNÍA",
        ),
        "PUERTO TEJADA" => array(
          "code" => "19573",
          "name" => "PUERTO TEJADA",
        ),
        "PURACÉ" => array(
          "code" => "19585",
          "name" => "PURACÉ",
        ),
        "ROSAS" => array(
          "code" => "19622",
          "name" => "ROSAS",
        ),
        "SAN SEBASTIÁN" => array(
          "code" => "19693",
          "name" => "SAN SEBASTIÁN",
        ),
        "SANTANDER DE QUILICHAO" => array(
          "code" => "19698",
          "name" => "SANTANDER DE QUILICHAO",
        ),
        "SANTA ROSA" => array(
          "code" => "19701",
          "name" => "SANTA ROSA",
        ),
        "SILVIA" => array(
          "code" => "19743",
          "name" => "SILVIA",
        ),
        "SOTARÁ PAISPAMBA" => array(
          "code" => "19760",
          "name" => "SOTARÁ PAISPAMBA",
        ),
        "SUÁREZ" => array(
          "code" => "19780",
          "name" => "SUÁREZ",
        ),
        "SUCRE" => array(
          "code" => "19785",
          "name" => "SUCRE",
        ),
        "TIMBÍO" => array(
          "code" => "19807",
          "name" => "TIMBÍO",
        ),
        "TIMBIQUÍ" => array(
          "code" => "19809",
          "name" => "TIMBIQUÍ",
        ),
        "TORIBÍO" => array(
          "code" => "19821",
          "name" => "TORIBÍO",
        ),
        "TOTORÓ" => array(
          "code" => "19824",
          "name" => "TOTORÓ",
        ),
        "VILLA RICA" => array(
          "code" => "19845",
          "name" => "VILLA RICA",
        ),
      ),
    ),
    "CALDAS" => array(
      "abbr" => "CAL",
      "code" => "17",
      "name" => "CALDAS",
      "cities" => array(
        "MANIZALES" => array(
          "code" => "17001",
          "name" => "MANIZALES",
        ),
        "AGUADAS" => array(
          "code" => "17013",
          "name" => "AGUADAS",
        ),
        "ANSERMA" => array(
          "code" => "17042",
          "name" => "ANSERMA",
        ),
        "ARANZAZU" => array(
          "code" => "17050",
          "name" => "ARANZAZU",
        ),
        "BELALCÁZAR" => array(
          "code" => "17088",
          "name" => "BELALCÁZAR",
        ),
        "CHINCHINÁ" => array(
          "code" => "17174",
          "name" => "CHINCHINÁ",
        ),
        "FILADELFIA" => array(
          "code" => "17272",
          "name" => "FILADELFIA",
        ),
        "LA DORADA" => array(
          "code" => "17380",
          "name" => "LA DORADA",
        ),
        "LA MERCED" => array(
          "code" => "17388",
          "name" => "LA MERCED",
        ),
        "MANZANARES" => array(
          "code" => "17433",
          "name" => "MANZANARES",
        ),
        "MARMATO" => array(
          "code" => "17442",
          "name" => "MARMATO",
        ),
        "MARQUETALIA" => array(
          "code" => "17444",
          "name" => "MARQUETALIA",
        ),
        "MARULANDA" => array(
          "code" => "17446",
          "name" => "MARULANDA",
        ),
        "NEIRA" => array(
          "code" => "17486",
          "name" => "NEIRA",
        ),
        "NORCASIA" => array(
          "code" => "17495",
          "name" => "NORCASIA",
        ),
        "PÁCORA" => array(
          "code" => "17513",
          "name" => "PÁCORA",
        ),
        "PALESTINA" => array(
          "code" => "17524",
          "name" => "PALESTINA",
        ),
        "PENSILVANIA" => array(
          "code" => "17541",
          "name" => "PENSILVANIA",
        ),
        "RIOSUCIO" => array(
          "code" => "17614",
          "name" => "RIOSUCIO",
        ),
        "RISARALDA" => array(
          "code" => "17616",
          "name" => "RISARALDA",
        ),
        "SALAMINA" => array(
          "code" => "17653",
          "name" => "SALAMINA",
        ),
        "SAMANÁ" => array(
          "code" => "17662",
          "name" => "SAMANÁ",
        ),
        "SAN JOSÉ" => array(
          "code" => "17665",
          "name" => "SAN JOSÉ",
        ),
        "SUPÍA" => array(
          "code" => "17777",
          "name" => "SUPÍA",
        ),
        "VICTORIA" => array(
          "code" => "17867",
          "name" => "VICTORIA",
        ),
        "VILLAMARÍA" => array(
          "code" => "17873",
          "name" => "VILLAMARÍA",
        ),
        "VITERBO" => array(
          "code" => "17877",
          "name" => "VITERBO",
        ),
      ),
    ),
    "CAQUETÁ" => array(
      "abbr" => "CAQ",
      "code" => "18",
      "name" => "CAQUETÁ",
      "cities" => array(
        "FLORENCIA" => array(
          "code" => "18001",
          "name" => "FLORENCIA",
        ),
        "ALBANIA" => array(
          "code" => "18029",
          "name" => "ALBANIA",
        ),
        "BELÉN DE LOS ANDAQUÍES" => array(
          "code" => "18094",
          "name" => "BELÉN DE LOS ANDAQUÍES",
        ),
        "CARTAGENA DEL CHAIRÁ" => array(
          "code" => "18150",
          "name" => "CARTAGENA DEL CHAIRÁ",
        ),
        "CURILLO" => array(
          "code" => "18205",
          "name" => "CURILLO",
        ),
        "EL DONCELLO" => array(
          "code" => "18247",
          "name" => "EL DONCELLO",
        ),
        "EL PAUJÍL" => array(
          "code" => "18256",
          "name" => "EL PAUJÍL",
        ),
        "LA MONTAÑITA" => array(
          "code" => "18410",
          "name" => "LA MONTAÑITA",
        ),
        "MILÁN" => array(
          "code" => "18460",
          "name" => "MILÁN",
        ),
        "MORELIA" => array(
          "code" => "18479",
          "name" => "MORELIA",
        ),
        "PUERTO RICO" => array(
          "code" => "18592",
          "name" => "PUERTO RICO",
        ),
        "SAN JOSÉ DEL FRAGUA" => array(
          "code" => "18610",
          "name" => "SAN JOSÉ DEL FRAGUA",
        ),
        "SAN VICENTE DEL CAGUÁN" => array(
          "code" => "18753",
          "name" => "SAN VICENTE DEL CAGUÁN",
        ),
        "SOLANO" => array(
          "code" => "18756",
          "name" => "SOLANO",
        ),
        "SOLITA" => array(
          "code" => "18785",
          "name" => "SOLITA",
        ),
        "VALPARAÍSO" => array(
          "code" => "18860",
          "name" => "VALPARAÍSO",
        ),
      ),
    ),
    "CESAR" => array(
      "abbr" => "CES",
      "code" => "20",
      "name" => "CESAR",
      "cities" => array(
        "VALLEDUPAR" => array(
          "code" => "20001",
          "name" => "VALLEDUPAR",
        ),
        "AGUACHICA" => array(
          "code" => "20011",
          "name" => "AGUACHICA",
        ),
        "AGUSTÍN CODAZZI" => array(
          "code" => "20013",
          "name" => "AGUSTÍN CODAZZI",
        ),
        "ASTREA" => array(
          "code" => "20032",
          "name" => "ASTREA",
        ),
        "BECERRIL" => array(
          "code" => "20045",
          "name" => "BECERRIL",
        ),
        "BOSCONIA" => array(
          "code" => "20060",
          "name" => "BOSCONIA",
        ),
        "CHIMICHAGUA" => array(
          "code" => "20175",
          "name" => "CHIMICHAGUA",
        ),
        "CHIRIGUANÁ" => array(
          "code" => "20178",
          "name" => "CHIRIGUANÁ",
        ),
        "CURUMANÍ" => array(
          "code" => "20228",
          "name" => "CURUMANÍ",
        ),
        "EL COPEY" => array(
          "code" => "20238",
          "name" => "EL COPEY",
        ),
        "EL PASO" => array(
          "code" => "20250",
          "name" => "EL PASO",
        ),
        "GAMARRA" => array(
          "code" => "20295",
          "name" => "GAMARRA",
        ),
        "GONZÁLEZ" => array(
          "code" => "20310",
          "name" => "GONZÁLEZ",
        ),
        "LA GLORIA" => array(
          "code" => "20383",
          "name" => "LA GLORIA",
        ),
        "LA JAGUA DE IBIRICO" => array(
          "code" => "20400",
          "name" => "LA JAGUA DE IBIRICO",
        ),
        "MANAURE BALCÓN DEL CESAR" => array(
          "code" => "20443",
          "name" => "MANAURE BALCÓN DEL CESAR",
        ),
        "PAILITAS" => array(
          "code" => "20517",
          "name" => "PAILITAS",
        ),
        "PELAYA" => array(
          "code" => "20550",
          "name" => "PELAYA",
        ),
        "PUEBLO BELLO" => array(
          "code" => "20570",
          "name" => "PUEBLO BELLO",
        ),
        "RÍO DE ORO" => array(
          "code" => "20614",
          "name" => "RÍO DE ORO",
        ),
        "LA PAZ" => array(
          "code" => "20621",
          "name" => "LA PAZ",
        ),
        "SAN ALBERTO" => array(
          "code" => "20710",
          "name" => "SAN ALBERTO",
        ),
        "SAN DIEGO" => array(
          "code" => "20750",
          "name" => "SAN DIEGO",
        ),
        "SAN MARTÍN" => array(
          "code" => "20770",
          "name" => "SAN MARTÍN",
        ),
        "TAMALAMEQUE" => array(
          "code" => "20787",
          "name" => "TAMALAMEQUE",
        ),
      ),
    ),
    "CÓRDOBA" => array(
      "abbr" => "COR",
      "code" => "23",
      "name" => "CÓRDOBA",
      "cities" => array(
        "MONTERÍA" => array(
          "code" => "23001",
          "name" => "MONTERÍA",
        ),
        "AYAPEL" => array(
          "code" => "23068",
          "name" => "AYAPEL",
        ),
        "BUENAVISTA" => array(
          "code" => "23079",
          "name" => "BUENAVISTA",
        ),
        "CANALETE" => array(
          "code" => "23090",
          "name" => "CANALETE",
        ),
        "CERETÉ" => array(
          "code" => "23162",
          "name" => "CERETÉ",
        ),
        "CHIMÁ" => array(
          "code" => "23168",
          "name" => "CHIMÁ",
        ),
        "CHINÚ" => array(
          "code" => "23182",
          "name" => "CHINÚ",
        ),
        "CIÉNAGA DE ORO" => array(
          "code" => "23189",
          "name" => "CIÉNAGA DE ORO",
        ),
        "COTORRA" => array(
          "code" => "23300",
          "name" => "COTORRA",
        ),
        "LA APARTADA" => array(
          "code" => "23350",
          "name" => "LA APARTADA",
        ),
        "LORICA" => array(
          "code" => "23417",
          "name" => "LORICA",
        ),
        "LOS CÓRDOBAS" => array(
          "code" => "23419",
          "name" => "LOS CÓRDOBAS",
        ),
        "MOMIL" => array(
          "code" => "23464",
          "name" => "MOMIL",
        ),
        "MONTELÍBANO" => array(
          "code" => "23466",
          "name" => "MONTELÍBANO",
        ),
        "MOÑITOS" => array(
          "code" => "23500",
          "name" => "MOÑITOS",
        ),
        "PLANETA RICA" => array(
          "code" => "23555",
          "name" => "PLANETA RICA",
        ),
        "PUEBLO NUEVO" => array(
          "code" => "23570",
          "name" => "PUEBLO NUEVO",
        ),
        "PUERTO ESCONDIDO" => array(
          "code" => "23574",
          "name" => "PUERTO ESCONDIDO",
        ),
        "PUERTO LIBERTADOR" => array(
          "code" => "23580",
          "name" => "PUERTO LIBERTADOR",
        ),
        "PURÍSIMA DE LA CONCEPCIÓN" => array(
          "code" => "23586",
          "name" => "PURÍSIMA DE LA CONCEPCIÓN",
        ),
        "SAHAGÚN" => array(
          "code" => "23660",
          "name" => "SAHAGÚN",
        ),
        "SAN ANDRÉS DE SOTAVENTO" => array(
          "code" => "23670",
          "name" => "SAN ANDRÉS DE SOTAVENTO",
        ),
        "SAN ANTERO" => array(
          "code" => "23672",
          "name" => "SAN ANTERO",
        ),
        "SAN BERNARDO DEL VIENTO" => array(
          "code" => "23675",
          "name" => "SAN BERNARDO DEL VIENTO",
        ),
        "SAN CARLOS" => array(
          "code" => "23678",
          "name" => "SAN CARLOS",
        ),
        "SAN JOSÉ DE URÉ" => array(
          "code" => "23682",
          "name" => "SAN JOSÉ DE URÉ",
        ),
        "SAN PELAYO" => array(
          "code" => "23686",
          "name" => "SAN PELAYO",
        ),
        "TIERRALTA" => array(
          "code" => "23807",
          "name" => "TIERRALTA",
        ),
        "TUCHÍN" => array(
          "code" => "23815",
          "name" => "TUCHÍN",
        ),
        "VALENCIA" => array(
          "code" => "23855",
          "name" => "VALENCIA",
        ),
      ),
    ),
    "CUNDINAMARCA" => array(
      "abbr" => "CUN",
      "code" => "25",
      "name" => "CUNDINAMARCA",
      "cities" => array(
        "AGUA DE DIOS" => array(
          "code" => "25001",
          "name" => "AGUA DE DIOS",
        ),
        "ALBÁN" => array(
          "code" => "25019",
          "name" => "ALBÁN",
        ),
        "ANAPOIMA" => array(
          "code" => "25035",
          "name" => "ANAPOIMA",
        ),
        "ANOLAIMA" => array(
          "code" => "25040",
          "name" => "ANOLAIMA",
        ),
        "ARBELÁEZ" => array(
          "code" => "25053",
          "name" => "ARBELÁEZ",
        ),
        "BELTRÁN" => array(
          "code" => "25086",
          "name" => "BELTRÁN",
        ),
        "BITUIMA" => array(
          "code" => "25095",
          "name" => "BITUIMA",
        ),
        "BOJACÁ" => array(
          "code" => "25099",
          "name" => "BOJACÁ",
        ),
        "CABRERA" => array(
          "code" => "25120",
          "name" => "CABRERA",
        ),
        "CACHIPAY" => array(
          "code" => "25123",
          "name" => "CACHIPAY",
        ),
        "CAJICÁ" => array(
          "code" => "25126",
          "name" => "CAJICÁ",
        ),
        "CAPARRAPÍ" => array(
          "code" => "25148",
          "name" => "CAPARRAPÍ",
        ),
        "CÁQUEZA" => array(
          "code" => "25151",
          "name" => "CÁQUEZA",
        ),
        "CARMEN DE CARUPA" => array(
          "code" => "25154",
          "name" => "CARMEN DE CARUPA",
        ),
        "CHAGUANÍ" => array(
          "code" => "25168",
          "name" => "CHAGUANÍ",
        ),
        "CHÍA" => array(
          "code" => "25175",
          "name" => "CHÍA",
        ),
        "CHIPAQUE" => array(
          "code" => "25178",
          "name" => "CHIPAQUE",
        ),
        "CHOACHÍ" => array(
          "code" => "25181",
          "name" => "CHOACHÍ",
        ),
        "CHOCONTÁ" => array(
          "code" => "25183",
          "name" => "CHOCONTÁ",
        ),
        "COGUA" => array(
          "code" => "25200",
          "name" => "COGUA",
        ),
        "COTA" => array(
          "code" => "25214",
          "name" => "COTA",
        ),
        "CUCUNUBÁ" => array(
          "code" => "25224",
          "name" => "CUCUNUBÁ",
        ),
        "EL COLEGIO" => array(
          "code" => "25245",
          "name" => "EL COLEGIO",
        ),
        "EL PEÑÓN" => array(
          "code" => "25258",
          "name" => "EL PEÑÓN",
        ),
        "EL ROSAL" => array(
          "code" => "25260",
          "name" => "EL ROSAL",
        ),
        "FACATATIVÁ" => array(
          "code" => "25269",
          "name" => "FACATATIVÁ",
        ),
        "FÓMEQUE" => array(
          "code" => "25279",
          "name" => "FÓMEQUE",
        ),
        "FOSCA" => array(
          "code" => "25281",
          "name" => "FOSCA",
        ),
        "FUNZA" => array(
          "code" => "25286",
          "name" => "FUNZA",
        ),
        "FÚQUENE" => array(
          "code" => "25288",
          "name" => "FÚQUENE",
        ),
        "FUSAGASUGÁ" => array(
          "code" => "25290",
          "name" => "FUSAGASUGÁ",
        ),
        "GACHALÁ" => array(
          "code" => "25293",
          "name" => "GACHALÁ",
        ),
        "GACHANCIPÁ" => array(
          "code" => "25295",
          "name" => "GACHANCIPÁ",
        ),
        "GACHETÁ" => array(
          "code" => "25297",
          "name" => "GACHETÁ",
        ),
        "GAMA" => array(
          "code" => "25299",
          "name" => "GAMA",
        ),
        "GIRARDOT" => array(
          "code" => "25307",
          "name" => "GIRARDOT",
        ),
        "GRANADA" => array(
          "code" => "25312",
          "name" => "GRANADA",
        ),
        "GUACHETÁ" => array(
          "code" => "25317",
          "name" => "GUACHETÁ",
        ),
        "GUADUAS" => array(
          "code" => "25320",
          "name" => "GUADUAS",
        ),
        "GUASCA" => array(
          "code" => "25322",
          "name" => "GUASCA",
        ),
        "GUATAQUÍ" => array(
          "code" => "25324",
          "name" => "GUATAQUÍ",
        ),
        "GUATAVITA" => array(
          "code" => "25326",
          "name" => "GUATAVITA",
        ),
        "GUAYABAL DE SÍQUIMA" => array(
          "code" => "25328",
          "name" => "GUAYABAL DE SÍQUIMA",
        ),
        "GUAYABETAL" => array(
          "code" => "25335",
          "name" => "GUAYABETAL",
        ),
        "GUTIÉRREZ" => array(
          "code" => "25339",
          "name" => "GUTIÉRREZ",
        ),
        "JERUSALÉN" => array(
          "code" => "25368",
          "name" => "JERUSALÉN",
        ),
        "JUNÍN" => array(
          "code" => "25372",
          "name" => "JUNÍN",
        ),
        "LA CALERA" => array(
          "code" => "25377",
          "name" => "LA CALERA",
        ),
        "LA MESA" => array(
          "code" => "25386",
          "name" => "LA MESA",
        ),
        "LA PALMA" => array(
          "code" => "25394",
          "name" => "LA PALMA",
        ),
        "LA PEÑA" => array(
          "code" => "25398",
          "name" => "LA PEÑA",
        ),
        "LA VEGA" => array(
          "code" => "25402",
          "name" => "LA VEGA",
        ),
        "LENGUAZAQUE" => array(
          "code" => "25407",
          "name" => "LENGUAZAQUE",
        ),
        "MACHETÁ" => array(
          "code" => "25426",
          "name" => "MACHETÁ",
        ),
        "MADRID" => array(
          "code" => "25430",
          "name" => "MADRID",
        ),
        "MANTA" => array(
          "code" => "25436",
          "name" => "MANTA",
        ),
        "MEDINA" => array(
          "code" => "25438",
          "name" => "MEDINA",
        ),
        "MOSQUERA" => array(
          "code" => "25473",
          "name" => "MOSQUERA",
        ),
        "NARIÑO" => array(
          "code" => "25483",
          "name" => "NARIÑO",
        ),
        "NEMOCÓN" => array(
          "code" => "25486",
          "name" => "NEMOCÓN",
        ),
        "NILO" => array(
          "code" => "25488",
          "name" => "NILO",
        ),
        "NIMAIMA" => array(
          "code" => "25489",
          "name" => "NIMAIMA",
        ),
        "NOCAIMA" => array(
          "code" => "25491",
          "name" => "NOCAIMA",
        ),
        "VENECIA" => array(
          "code" => "25506",
          "name" => "VENECIA",
        ),
        "PACHO" => array(
          "code" => "25513",
          "name" => "PACHO",
        ),
        "PAIME" => array(
          "code" => "25518",
          "name" => "PAIME",
        ),
        "PANDI" => array(
          "code" => "25524",
          "name" => "PANDI",
        ),
        "PARATEBUENO" => array(
          "code" => "25530",
          "name" => "PARATEBUENO",
        ),
        "PASCA" => array(
          "code" => "25535",
          "name" => "PASCA",
        ),
        "PUERTO SALGAR" => array(
          "code" => "25572",
          "name" => "PUERTO SALGAR",
        ),
        "PULÍ" => array(
          "code" => "25580",
          "name" => "PULÍ",
        ),
        "QUEBRADANEGRA" => array(
          "code" => "25592",
          "name" => "QUEBRADANEGRA",
        ),
        "QUETAME" => array(
          "code" => "25594",
          "name" => "QUETAME",
        ),
        "QUIPILE" => array(
          "code" => "25596",
          "name" => "QUIPILE",
        ),
        "APULO" => array(
          "code" => "25599",
          "name" => "APULO",
        ),
        "RICAURTE" => array(
          "code" => "25612",
          "name" => "RICAURTE",
        ),
        "SAN ANTONIO DEL TEQUENDAMA" => array(
          "code" => "25645",
          "name" => "SAN ANTONIO DEL TEQUENDAMA",
        ),
        "SAN BERNARDO" => array(
          "code" => "25649",
          "name" => "SAN BERNARDO",
        ),
        "SAN CAYETANO" => array(
          "code" => "25653",
          "name" => "SAN CAYETANO",
        ),
        "SAN FRANCISCO" => array(
          "code" => "25658",
          "name" => "SAN FRANCISCO",
        ),
        "SAN JUAN DE RIOSECO" => array(
          "code" => "25662",
          "name" => "SAN JUAN DE RIOSECO",
        ),
        "SASAIMA" => array(
          "code" => "25718",
          "name" => "SASAIMA",
        ),
        "SESQUILÉ" => array(
          "code" => "25736",
          "name" => "SESQUILÉ",
        ),
        "SIBATÉ" => array(
          "code" => "25740",
          "name" => "SIBATÉ",
        ),
        "SILVANIA" => array(
          "code" => "25743",
          "name" => "SILVANIA",
        ),
        "SIMIJACA" => array(
          "code" => "25745",
          "name" => "SIMIJACA",
        ),
        "SOACHA" => array(
          "code" => "25754",
          "name" => "SOACHA",
        ),
        "SOPÓ" => array(
          "code" => "25758",
          "name" => "SOPÓ",
        ),
        "SUBACHOQUE" => array(
          "code" => "25769",
          "name" => "SUBACHOQUE",
        ),
        "SUESCA" => array(
          "code" => "25772",
          "name" => "SUESCA",
        ),
        "SUPATÁ" => array(
          "code" => "25777",
          "name" => "SUPATÁ",
        ),
        "SUSA" => array(
          "code" => "25779",
          "name" => "SUSA",
        ),
        "SUTATAUSA" => array(
          "code" => "25781",
          "name" => "SUTATAUSA",
        ),
        "TABIO" => array(
          "code" => "25785",
          "name" => "TABIO",
        ),
        "TAUSA" => array(
          "code" => "25793",
          "name" => "TAUSA",
        ),
        "TENA" => array(
          "code" => "25797",
          "name" => "TENA",
        ),
        "TENJO" => array(
          "code" => "25799",
          "name" => "TENJO",
        ),
        "TIBACUY" => array(
          "code" => "25805",
          "name" => "TIBACUY",
        ),
        "TIBIRITA" => array(
          "code" => "25807",
          "name" => "TIBIRITA",
        ),
        "TOCAIMA" => array(
          "code" => "25815",
          "name" => "TOCAIMA",
        ),
        "TOCANCIPÁ" => array(
          "code" => "25817",
          "name" => "TOCANCIPÁ",
        ),
        "TOPAIPÍ" => array(
          "code" => "25823",
          "name" => "TOPAIPÍ",
        ),
        "UBALÁ" => array(
          "code" => "25839",
          "name" => "UBALÁ",
        ),
        "UBAQUE" => array(
          "code" => "25841",
          "name" => "UBAQUE",
        ),
        "VILLA DE SAN DIEGO DE UBATÉ" => array(
          "code" => "25843",
          "name" => "VILLA DE SAN DIEGO DE UBATÉ",
        ),
        "UNE" => array(
          "code" => "25845",
          "name" => "UNE",
        ),
        "ÚTICA" => array(
          "code" => "25851",
          "name" => "ÚTICA",
        ),
        "VERGARA" => array(
          "code" => "25862",
          "name" => "VERGARA",
        ),
        "VIANÍ" => array(
          "code" => "25867",
          "name" => "VIANÍ",
        ),
        "VILLAGÓMEZ" => array(
          "code" => "25871",
          "name" => "VILLAGÓMEZ",
        ),
        "VILLAPINZÓN" => array(
          "code" => "25873",
          "name" => "VILLAPINZÓN",
        ),
        "VILLETA" => array(
          "code" => "25875",
          "name" => "VILLETA",
        ),
        "VIOTÁ" => array(
          "code" => "25878",
          "name" => "VIOTÁ",
        ),
        "YACOPÍ" => array(
          "code" => "25885",
          "name" => "YACOPÍ",
        ),
        "ZIPACÓN" => array(
          "code" => "25898",
          "name" => "ZIPACÓN",
        ),
        "ZIPAQUIRÁ" => array(
          "code" => "25899",
          "name" => "ZIPAQUIRÁ",
        ),
      ),
    ),
    "CHOCÓ" => array(
      "abbr" => "CHO",
      "code" => "27",
      "name" => "CHOCÓ",
      "cities" => array(
        "QUIBDÓ" => array(
          "code" => "27001",
          "name" => "QUIBDÓ",
        ),
        "ACANDÍ" => array(
          "code" => "27006",
          "name" => "ACANDÍ",
        ),
        "ALTO BAUDÓ" => array(
          "code" => "27025",
          "name" => "ALTO BAUDÓ",
        ),
        "ATRATO" => array(
          "code" => "27050",
          "name" => "ATRATO",
        ),
        "BAGADÓ" => array(
          "code" => "27073",
          "name" => "BAGADÓ",
        ),
        "BAHÍA SOLANO" => array(
          "code" => "27075",
          "name" => "BAHÍA SOLANO",
        ),
        "BAJO BAUDÓ" => array(
          "code" => "27077",
          "name" => "BAJO BAUDÓ",
        ),
        "BOJAYÁ" => array(
          "code" => "27099",
          "name" => "BOJAYÁ",
        ),
        "EL CANTÓN DEL SAN PABLO" => array(
          "code" => "27135",
          "name" => "EL CANTÓN DEL SAN PABLO",
        ),
        "CARMEN DEL DARIÉN" => array(
          "code" => "27150",
          "name" => "CARMEN DEL DARIÉN",
        ),
        "CÉRTEGUI" => array(
          "code" => "27160",
          "name" => "CÉRTEGUI",
        ),
        "CONDOTO" => array(
          "code" => "27205",
          "name" => "CONDOTO",
        ),
        "EL CARMEN DE ATRATO" => array(
          "code" => "27245",
          "name" => "EL CARMEN DE ATRATO",
        ),
        "EL LITORAL DEL SAN JUAN" => array(
          "code" => "27250",
          "name" => "EL LITORAL DEL SAN JUAN",
        ),
        "ISTMINA" => array(
          "code" => "27361",
          "name" => "ISTMINA",
        ),
        "JURADÓ" => array(
          "code" => "27372",
          "name" => "JURADÓ",
        ),
        "LLORÓ" => array(
          "code" => "27413",
          "name" => "LLORÓ",
        ),
        "MEDIO ATRATO" => array(
          "code" => "27425",
          "name" => "MEDIO ATRATO",
        ),
        "MEDIO BAUDÓ" => array(
          "code" => "27430",
          "name" => "MEDIO BAUDÓ",
        ),
        "MEDIO SAN JUAN" => array(
          "code" => "27450",
          "name" => "MEDIO SAN JUAN",
        ),
        "NÓVITA" => array(
          "code" => "27491",
          "name" => "NÓVITA",
        ),
        "NUQUÍ" => array(
          "code" => "27495",
          "name" => "NUQUÍ",
        ),
        "RÍO IRÓ" => array(
          "code" => "27580",
          "name" => "RÍO IRÓ",
        ),
        "RÍO QUITO" => array(
          "code" => "27600",
          "name" => "RÍO QUITO",
        ),
        "RIOSUCIO" => array(
          "code" => "27615",
          "name" => "RIOSUCIO",
        ),
        "SAN JOSÉ DEL PALMAR" => array(
          "code" => "27660",
          "name" => "SAN JOSÉ DEL PALMAR",
        ),
        "SIPÍ" => array(
          "code" => "27745",
          "name" => "SIPÍ",
        ),
        "TADÓ" => array(
          "code" => "27787",
          "name" => "TADÓ",
        ),
        "UNGUÍA" => array(
          "code" => "27800",
          "name" => "UNGUÍA",
        ),
        "UNIÓN PANAMERICANA" => array(
          "code" => "27810",
          "name" => "UNIÓN PANAMERICANA",
        ),
      ),
    ),
    "HUILA" => array(
      "abbr" => "HUI",
      "code" => "41",
      "name" => "HUILA",
      "cities" => array(
        "NEIVA" => array(
          "code" => "41001",
          "name" => "NEIVA",
        ),
        "ACEVEDO" => array(
          "code" => "41006",
          "name" => "ACEVEDO",
        ),
        "AGRADO" => array(
          "code" => "41013",
          "name" => "AGRADO",
        ),
        "AIPE" => array(
          "code" => "41016",
          "name" => "AIPE",
        ),
        "ALGECIRAS" => array(
          "code" => "41020",
          "name" => "ALGECIRAS",
        ),
        "ALTAMIRA" => array(
          "code" => "41026",
          "name" => "ALTAMIRA",
        ),
        "BARAYA" => array(
          "code" => "41078",
          "name" => "BARAYA",
        ),
        "CAMPOALEGRE" => array(
          "code" => "41132",
          "name" => "CAMPOALEGRE",
        ),
        "COLOMBIA" => array(
          "code" => "41206",
          "name" => "COLOMBIA",
        ),
        "ELÍAS" => array(
          "code" => "41244",
          "name" => "ELÍAS",
        ),
        "GARZÓN" => array(
          "code" => "41298",
          "name" => "GARZÓN",
        ),
        "GIGANTE" => array(
          "code" => "41306",
          "name" => "GIGANTE",
        ),
        "GUADALUPE" => array(
          "code" => "41319",
          "name" => "GUADALUPE",
        ),
        "HOBO" => array(
          "code" => "41349",
          "name" => "HOBO",
        ),
        "ÍQUIRA" => array(
          "code" => "41357",
          "name" => "ÍQUIRA",
        ),
        "ISNOS" => array(
          "code" => "41359",
          "name" => "ISNOS",
        ),
        "LA ARGENTINA" => array(
          "code" => "41378",
          "name" => "LA ARGENTINA",
        ),
        "LA PLATA" => array(
          "code" => "41396",
          "name" => "LA PLATA",
        ),
        "NÁTAGA" => array(
          "code" => "41483",
          "name" => "NÁTAGA",
        ),
        "OPORAPA" => array(
          "code" => "41503",
          "name" => "OPORAPA",
        ),
        "PAICOL" => array(
          "code" => "41518",
          "name" => "PAICOL",
        ),
        "PALERMO" => array(
          "code" => "41524",
          "name" => "PALERMO",
        ),
        "PALESTINA" => array(
          "code" => "41530",
          "name" => "PALESTINA",
        ),
        "PITAL" => array(
          "code" => "41548",
          "name" => "PITAL",
        ),
        "PITALITO" => array(
          "code" => "41551",
          "name" => "PITALITO",
        ),
        "RIVERA" => array(
          "code" => "41615",
          "name" => "RIVERA",
        ),
        "SALADOBLANCO" => array(
          "code" => "41660",
          "name" => "SALADOBLANCO",
        ),
        "SAN AGUSTÍN" => array(
          "code" => "41668",
          "name" => "SAN AGUSTÍN",
        ),
        "SANTA MARÍA" => array(
          "code" => "41676",
          "name" => "SANTA MARÍA",
        ),
        "SUAZA" => array(
          "code" => "41770",
          "name" => "SUAZA",
        ),
        "TARQUI" => array(
          "code" => "41791",
          "name" => "TARQUI",
        ),
        "TESALIA" => array(
          "code" => "41797",
          "name" => "TESALIA",
        ),
        "TELLO" => array(
          "code" => "41799",
          "name" => "TELLO",
        ),
        "TERUEL" => array(
          "code" => "41801",
          "name" => "TERUEL",
        ),
        "TIMANÁ" => array(
          "code" => "41807",
          "name" => "TIMANÁ",
        ),
        "VILLAVIEJA" => array(
          "code" => "41872",
          "name" => "VILLAVIEJA",
        ),
        "YAGUARÁ" => array(
          "code" => "41885",
          "name" => "YAGUARÁ",
        ),
      ),
    ),
    "LA GUAJIRA" => array(
      "abbr" => "GUJ",
      "code" => "44",
      "name" => "LA GUAJIRA",
      "cities" => array(
        "RIOHACHA" => array(
          "code" => "44001",
          "name" => "RIOHACHA",
        ),
        "ALBANIA" => array(
          "code" => "44035",
          "name" => "ALBANIA",
        ),
        "BARRANCAS" => array(
          "code" => "44078",
          "name" => "BARRANCAS",
        ),
        "DIBULLA" => array(
          "code" => "44090",
          "name" => "DIBULLA",
        ),
        "DISTRACCIÓN" => array(
          "code" => "44098",
          "name" => "DISTRACCIÓN",
        ),
        "EL MOLINO" => array(
          "code" => "44110",
          "name" => "EL MOLINO",
        ),
        "FONSECA" => array(
          "code" => "44279",
          "name" => "FONSECA",
        ),
        "HATONUEVO" => array(
          "code" => "44378",
          "name" => "HATONUEVO",
        ),
        "LA JAGUA DEL PILAR" => array(
          "code" => "44420",
          "name" => "LA JAGUA DEL PILAR",
        ),
        "MAICAO" => array(
          "code" => "44430",
          "name" => "MAICAO",
        ),
        "MANAURE" => array(
          "code" => "44560",
          "name" => "MANAURE",
        ),
        "SAN JUAN DEL CESAR" => array(
          "code" => "44650",
          "name" => "SAN JUAN DEL CESAR",
        ),
        "URIBIA" => array(
          "code" => "44847",
          "name" => "URIBIA",
        ),
        "URUMITA" => array(
          "code" => "44855",
          "name" => "URUMITA",
        ),
        "VILLANUEVA" => array(
          "code" => "44874",
          "name" => "VILLANUEVA",
        ),
      ),
    ),
    "MAGDALENA" => array(
      "abbr" => "MAG",
      "code" => "47",
      "name" => "MAGDALENA",
      "cities" => array(
        "SANTA MARTA" => array(
          "code" => "47001",
          "name" => "SANTA MARTA",
        ),
        "ALGARROBO" => array(
          "code" => "47030",
          "name" => "ALGARROBO",
        ),
        "ARACATACA" => array(
          "code" => "47053",
          "name" => "ARACATACA",
        ),
        "ARIGUANÍ" => array(
          "code" => "47058",
          "name" => "ARIGUANÍ",
        ),
        "CERRO DE SAN ANTONIO" => array(
          "code" => "47161",
          "name" => "CERRO DE SAN ANTONIO",
        ),
        "CHIVOLO" => array(
          "code" => "47170",
          "name" => "CHIVOLO",
        ),
        "CIÉNAGA" => array(
          "code" => "47189",
          "name" => "CIÉNAGA",
        ),
        "CONCORDIA" => array(
          "code" => "47205",
          "name" => "CONCORDIA",
        ),
        "EL BANCO" => array(
          "code" => "47245",
          "name" => "EL BANCO",
        ),
        "EL PIÑÓN" => array(
          "code" => "47258",
          "name" => "EL PIÑÓN",
        ),
        "EL RETÉN" => array(
          "code" => "47268",
          "name" => "EL RETÉN",
        ),
        "FUNDACIÓN" => array(
          "code" => "47288",
          "name" => "FUNDACIÓN",
        ),
        "GUAMAL" => array(
          "code" => "47318",
          "name" => "GUAMAL",
        ),
        "NUEVA GRANADA" => array(
          "code" => "47460",
          "name" => "NUEVA GRANADA",
        ),
        "PEDRAZA" => array(
          "code" => "47541",
          "name" => "PEDRAZA",
        ),
        "PIJIÑO DEL CARMEN" => array(
          "code" => "47545",
          "name" => "PIJIÑO DEL CARMEN",
        ),
        "PIVIJAY" => array(
          "code" => "47551",
          "name" => "PIVIJAY",
        ),
        "PLATO" => array(
          "code" => "47555",
          "name" => "PLATO",
        ),
        "PUEBLOVIEJO" => array(
          "code" => "47570",
          "name" => "PUEBLOVIEJO",
        ),
        "REMOLINO" => array(
          "code" => "47605",
          "name" => "REMOLINO",
        ),
        "SABANAS DE SAN ÁNGEL" => array(
          "code" => "47660",
          "name" => "SABANAS DE SAN ÁNGEL",
        ),
        "SALAMINA" => array(
          "code" => "47675",
          "name" => "SALAMINA",
        ),
        "SAN SEBASTIÁN DE BUENAVISTA" => array(
          "code" => "47692",
          "name" => "SAN SEBASTIÁN DE BUENAVISTA",
        ),
        "SAN ZENÓN" => array(
          "code" => "47703",
          "name" => "SAN ZENÓN",
        ),
        "SANTA ANA" => array(
          "code" => "47707",
          "name" => "SANTA ANA",
        ),
        "SANTA BÁRBARA DE PINTO" => array(
          "code" => "47720",
          "name" => "SANTA BÁRBARA DE PINTO",
        ),
        "SITIONUEVO" => array(
          "code" => "47745",
          "name" => "SITIONUEVO",
        ),
        "TENERIFE" => array(
          "code" => "47798",
          "name" => "TENERIFE",
        ),
        "ZAPAYÁN" => array(
          "code" => "47960",
          "name" => "ZAPAYÁN",
        ),
        "ZONA BANANERA" => array(
          "code" => "47980",
          "name" => "ZONA BANANERA",
        ),
      ),
    ),
    "META" => array(
      "abbr" => "MET",
      "code" => "50",
      "name" => "META",
      "cities" => array(
        "VILLAVICENCIO" => array(
          "code" => "50001",
          "name" => "VILLAVICENCIO",
        ),
        "ACACÍAS" => array(
          "code" => "50006",
          "name" => "ACACÍAS",
        ),
        "BARRANCA DE UPÍA" => array(
          "code" => "50110",
          "name" => "BARRANCA DE UPÍA",
        ),
        "CABUYARO" => array(
          "code" => "50124",
          "name" => "CABUYARO",
        ),
        "CASTILLA LA NUEVA" => array(
          "code" => "50150",
          "name" => "CASTILLA LA NUEVA",
        ),
        "CUBARRAL" => array(
          "code" => "50223",
          "name" => "CUBARRAL",
        ),
        "CUMARAL" => array(
          "code" => "50226",
          "name" => "CUMARAL",
        ),
        "EL CALVARIO" => array(
          "code" => "50245",
          "name" => "EL CALVARIO",
        ),
        "EL CASTILLO" => array(
          "code" => "50251",
          "name" => "EL CASTILLO",
        ),
        "EL DORADO" => array(
          "code" => "50270",
          "name" => "EL DORADO",
        ),
        "FUENTE DE ORO" => array(
          "code" => "50287",
          "name" => "FUENTE DE ORO",
        ),
        "GRANADA" => array(
          "code" => "50313",
          "name" => "GRANADA",
        ),
        "GUAMAL" => array(
          "code" => "50318",
          "name" => "GUAMAL",
        ),
        "MAPIRIPÁN" => array(
          "code" => "50325",
          "name" => "MAPIRIPÁN",
        ),
        "MESETAS" => array(
          "code" => "50330",
          "name" => "MESETAS",
        ),
        "LA MACARENA" => array(
          "code" => "50350",
          "name" => "LA MACARENA",
        ),
        "URIBE" => array(
          "code" => "50370",
          "name" => "URIBE",
        ),
        "LEJANÍAS" => array(
          "code" => "50400",
          "name" => "LEJANÍAS",
        ),
        "PUERTO CONCORDIA" => array(
          "code" => "50450",
          "name" => "PUERTO CONCORDIA",
        ),
        "PUERTO GAITÁN" => array(
          "code" => "50568",
          "name" => "PUERTO GAITÁN",
        ),
        "PUERTO LÓPEZ" => array(
          "code" => "50573",
          "name" => "PUERTO LÓPEZ",
        ),
        "PUERTO LLERAS" => array(
          "code" => "50577",
          "name" => "PUERTO LLERAS",
        ),
        "PUERTO RICO" => array(
          "code" => "50590",
          "name" => "PUERTO RICO",
        ),
        "RESTREPO" => array(
          "code" => "50606",
          "name" => "RESTREPO",
        ),
        "SAN CARLOS DE GUAROA" => array(
          "code" => "50680",
          "name" => "SAN CARLOS DE GUAROA",
        ),
        "SAN JUAN DE ARAMA" => array(
          "code" => "50683",
          "name" => "SAN JUAN DE ARAMA",
        ),
        "SAN JUANITO" => array(
          "code" => "50686",
          "name" => "SAN JUANITO",
        ),
        "SAN MARTÍN" => array(
          "code" => "50689",
          "name" => "SAN MARTÍN",
        ),
        "VISTAHERMOSA" => array(
          "code" => "50711",
          "name" => "VISTAHERMOSA",
        ),
      ),
    ),
    "NARIÑO" => array(
      "abbr" => "NAR",
      "code" => "52",
      "name" => "NARIÑO",
      "cities" => array(
        "PASTO" => array(
          "code" => "52001",
          "name" => "PASTO",
        ),
        "ALBÁN" => array(
          "code" => "52019",
          "name" => "ALBÁN",
        ),
        "ALDANA" => array(
          "code" => "52022",
          "name" => "ALDANA",
        ),
        "ANCUYA" => array(
          "code" => "52036",
          "name" => "ANCUYA",
        ),
        "ARBOLEDA" => array(
          "code" => "52051",
          "name" => "ARBOLEDA",
        ),
        "BARBACOAS" => array(
          "code" => "52079",
          "name" => "BARBACOAS",
        ),
        "BELÉN" => array(
          "code" => "52083",
          "name" => "BELÉN",
        ),
        "BUESACO" => array(
          "code" => "52110",
          "name" => "BUESACO",
        ),
        "COLÓN" => array(
          "code" => "52203",
          "name" => "COLÓN",
        ),
        "CONSACÁ" => array(
          "code" => "52207",
          "name" => "CONSACÁ",
        ),
        "CONTADERO" => array(
          "code" => "52210",
          "name" => "CONTADERO",
        ),
        "CÓRDOBA" => array(
          "code" => "52215",
          "name" => "CÓRDOBA",
        ),
        "CUASPUD CARLOSAMA" => array(
          "code" => "52224",
          "name" => "CUASPUD CARLOSAMA",
        ),
        "CUMBAL" => array(
          "code" => "52227",
          "name" => "CUMBAL",
        ),
        "CUMBITARA" => array(
          "code" => "52233",
          "name" => "CUMBITARA",
        ),
        "CHACHAGÜÍ" => array(
          "code" => "52240",
          "name" => "CHACHAGÜÍ",
        ),
        "EL CHARCO" => array(
          "code" => "52250",
          "name" => "EL CHARCO",
        ),
        "EL PEÑOL" => array(
          "code" => "52254",
          "name" => "EL PEÑOL",
        ),
        "EL ROSARIO" => array(
          "code" => "52256",
          "name" => "EL ROSARIO",
        ),
        "EL TABLÓN DE GÓMEZ" => array(
          "code" => "52258",
          "name" => "EL TABLÓN DE GÓMEZ",
        ),
        "EL TAMBO" => array(
          "code" => "52260",
          "name" => "EL TAMBO",
        ),
        "FUNES" => array(
          "code" => "52287",
          "name" => "FUNES",
        ),
        "GUACHUCAL" => array(
          "code" => "52317",
          "name" => "GUACHUCAL",
        ),
        "GUAITARILLA" => array(
          "code" => "52320",
          "name" => "GUAITARILLA",
        ),
        "GUALMATÁN" => array(
          "code" => "52323",
          "name" => "GUALMATÁN",
        ),
        "ILES" => array(
          "code" => "52352",
          "name" => "ILES",
        ),
        "IMUÉS" => array(
          "code" => "52354",
          "name" => "IMUÉS",
        ),
        "IPIALES" => array(
          "code" => "52356",
          "name" => "IPIALES",
        ),
        "LA CRUZ" => array(
          "code" => "52378",
          "name" => "LA CRUZ",
        ),
        "LA FLORIDA" => array(
          "code" => "52381",
          "name" => "LA FLORIDA",
        ),
        "LA LLANADA" => array(
          "code" => "52385",
          "name" => "LA LLANADA",
        ),
        "LA TOLA" => array(
          "code" => "52390",
          "name" => "LA TOLA",
        ),
        "LA UNIÓN" => array(
          "code" => "52399",
          "name" => "LA UNIÓN",
        ),
        "LEIVA" => array(
          "code" => "52405",
          "name" => "LEIVA",
        ),
        "LINARES" => array(
          "code" => "52411",
          "name" => "LINARES",
        ),
        "LOS ANDES" => array(
          "code" => "52418",
          "name" => "LOS ANDES",
        ),
        "MAGÜÍ" => array(
          "code" => "52427",
          "name" => "MAGÜÍ",
        ),
        "MALLAMA" => array(
          "code" => "52435",
          "name" => "MALLAMA",
        ),
        "MOSQUERA" => array(
          "code" => "52473",
          "name" => "MOSQUERA",
        ),
        "NARIÑO" => array(
          "code" => "52480",
          "name" => "NARIÑO",
        ),
        "OLAYA HERRERA" => array(
          "code" => "52490",
          "name" => "OLAYA HERRERA",
        ),
        "OSPINA" => array(
          "code" => "52506",
          "name" => "OSPINA",
        ),
        "FRANCISCO PIZARRO" => array(
          "code" => "52520",
          "name" => "FRANCISCO PIZARRO",
        ),
        "POLICARPA" => array(
          "code" => "52540",
          "name" => "POLICARPA",
        ),
        "POTOSÍ" => array(
          "code" => "52560",
          "name" => "POTOSÍ",
        ),
        "PROVIDENCIA" => array(
          "code" => "52565",
          "name" => "PROVIDENCIA",
        ),
        "PUERRES" => array(
          "code" => "52573",
          "name" => "PUERRES",
        ),
        "PUPIALES" => array(
          "code" => "52585",
          "name" => "PUPIALES",
        ),
        "RICAURTE" => array(
          "code" => "52612",
          "name" => "RICAURTE",
        ),
        "ROBERTO PAYÁN" => array(
          "code" => "52621",
          "name" => "ROBERTO PAYÁN",
        ),
        "SAMANIEGO" => array(
          "code" => "52678",
          "name" => "SAMANIEGO",
        ),
        "SANDONÁ" => array(
          "code" => "52683",
          "name" => "SANDONÁ",
        ),
        "SAN BERNARDO" => array(
          "code" => "52685",
          "name" => "SAN BERNARDO",
        ),
        "SAN LORENZO" => array(
          "code" => "52687",
          "name" => "SAN LORENZO",
        ),
        "SAN PABLO" => array(
          "code" => "52693",
          "name" => "SAN PABLO",
        ),
        "SAN PEDRO DE CARTAGO" => array(
          "code" => "52694",
          "name" => "SAN PEDRO DE CARTAGO",
        ),
        "SANTA BÁRBARA" => array(
          "code" => "52696",
          "name" => "SANTA BÁRBARA",
        ),
        "SANTACRUZ" => array(
          "code" => "52699",
          "name" => "SANTACRUZ",
        ),
        "SAPUYES" => array(
          "code" => "52720",
          "name" => "SAPUYES",
        ),
        "TAMINANGO" => array(
          "code" => "52786",
          "name" => "TAMINANGO",
        ),
        "TANGUA" => array(
          "code" => "52788",
          "name" => "TANGUA",
        ),
        "SAN ANDRÉS DE TUMACO" => array(
          "code" => "52835",
          "name" => "SAN ANDRÉS DE TUMACO",
        ),
        "TÚQUERRES" => array(
          "code" => "52838",
          "name" => "TÚQUERRES",
        ),
        "YACUANQUER" => array(
          "code" => "52885",
          "name" => "YACUANQUER",
        ),
      ),
    ),
    "NORTE DE SANTANDER" => array(
      "abbr" => "NOR",
      "code" => "54",
      "name" => "NORTE DE SANTANDER",
      "cities" => array(
        "SAN JOSÉ DE CÚCUTA" => array(
          "code" => "54001",
          "name" => "SAN JOSÉ DE CÚCUTA",
        ),
        "ÁBREGO" => array(
          "code" => "54003",
          "name" => "ÁBREGO",
        ),
        "ARBOLEDAS" => array(
          "code" => "54051",
          "name" => "ARBOLEDAS",
        ),
        "BOCHALEMA" => array(
          "code" => "54099",
          "name" => "BOCHALEMA",
        ),
        "BUCARASICA" => array(
          "code" => "54109",
          "name" => "BUCARASICA",
        ),
        "CÁCOTA" => array(
          "code" => "54125",
          "name" => "CÁCOTA",
        ),
        "CÁCHIRA" => array(
          "code" => "54128",
          "name" => "CÁCHIRA",
        ),
        "CHINÁCOTA" => array(
          "code" => "54172",
          "name" => "CHINÁCOTA",
        ),
        "CHITAGÁ" => array(
          "code" => "54174",
          "name" => "CHITAGÁ",
        ),
        "CONVENCIÓN" => array(
          "code" => "54206",
          "name" => "CONVENCIÓN",
        ),
        "CUCUTILLA" => array(
          "code" => "54223",
          "name" => "CUCUTILLA",
        ),
        "DURANIA" => array(
          "code" => "54239",
          "name" => "DURANIA",
        ),
        "EL CARMEN" => array(
          "code" => "54245",
          "name" => "EL CARMEN",
        ),
        "EL TARRA" => array(
          "code" => "54250",
          "name" => "EL TARRA",
        ),
        "EL ZULIA" => array(
          "code" => "54261",
          "name" => "EL ZULIA",
        ),
        "GRAMALOTE" => array(
          "code" => "54313",
          "name" => "GRAMALOTE",
        ),
        "HACARÍ" => array(
          "code" => "54344",
          "name" => "HACARÍ",
        ),
        "HERRÁN" => array(
          "code" => "54347",
          "name" => "HERRÁN",
        ),
        "LABATECA" => array(
          "code" => "54377",
          "name" => "LABATECA",
        ),
        "LA ESPERANZA" => array(
          "code" => "54385",
          "name" => "LA ESPERANZA",
        ),
        "LA PLAYA" => array(
          "code" => "54398",
          "name" => "LA PLAYA",
        ),
        "LOS PATIOS" => array(
          "code" => "54405",
          "name" => "LOS PATIOS",
        ),
        "LOURDES" => array(
          "code" => "54418",
          "name" => "LOURDES",
        ),
        "MUTISCUA" => array(
          "code" => "54480",
          "name" => "MUTISCUA",
        ),
        "OCAÑA" => array(
          "code" => "54498",
          "name" => "OCAÑA",
        ),
        "PAMPLONA" => array(
          "code" => "54518",
          "name" => "PAMPLONA",
        ),
        "PAMPLONITA" => array(
          "code" => "54520",
          "name" => "PAMPLONITA",
        ),
        "PUERTO SANTANDER" => array(
          "code" => "54553",
          "name" => "PUERTO SANTANDER",
        ),
        "RAGONVALIA" => array(
          "code" => "54599",
          "name" => "RAGONVALIA",
        ),
        "SALAZAR" => array(
          "code" => "54660",
          "name" => "SALAZAR",
        ),
        "SAN CALIXTO" => array(
          "code" => "54670",
          "name" => "SAN CALIXTO",
        ),
        "SAN CAYETANO" => array(
          "code" => "54673",
          "name" => "SAN CAYETANO",
        ),
        "SANTIAGO" => array(
          "code" => "54680",
          "name" => "SANTIAGO",
        ),
        "SARDINATA" => array(
          "code" => "54720",
          "name" => "SARDINATA",
        ),
        "SILOS" => array(
          "code" => "54743",
          "name" => "SILOS",
        ),
        "TEORAMA" => array(
          "code" => "54800",
          "name" => "TEORAMA",
        ),
        "TIBÚ" => array(
          "code" => "54810",
          "name" => "TIBÚ",
        ),
        "TOLEDO" => array(
          "code" => "54820",
          "name" => "TOLEDO",
        ),
        "VILLA CARO" => array(
          "code" => "54871",
          "name" => "VILLA CARO",
        ),
        "VILLA DEL ROSARIO" => array(
          "code" => "54874",
          "name" => "VILLA DEL ROSARIO",
        ),
      ),
    ),
    "QUINDÍO" => array(
      "abbr" => "QUI",
      "code" => "63",
      "name" => "QUINDÍO",
      "cities" => array(
        "ARMENIA" => array(
          "code" => "63001",
          "name" => "ARMENIA",
        ),
        "BUENAVISTA" => array(
          "code" => "63111",
          "name" => "BUENAVISTA",
        ),
        "CALARCÁ" => array(
          "code" => "63130",
          "name" => "CALARCÁ",
        ),
        "CIRCASIA" => array(
          "code" => "63190",
          "name" => "CIRCASIA",
        ),
        "CÓRDOBA" => array(
          "code" => "63212",
          "name" => "CÓRDOBA",
        ),
        "FILANDIA" => array(
          "code" => "63272",
          "name" => "FILANDIA",
        ),
        "GÉNOVA" => array(
          "code" => "63302",
          "name" => "GÉNOVA",
        ),
        "LA TEBAIDA" => array(
          "code" => "63401",
          "name" => "LA TEBAIDA",
        ),
        "MONTENEGRO" => array(
          "code" => "63470",
          "name" => "MONTENEGRO",
        ),
        "PIJAO" => array(
          "code" => "63548",
          "name" => "PIJAO",
        ),
        "QUIMBAYA" => array(
          "code" => "63594",
          "name" => "QUIMBAYA",
        ),
        "SALENTO" => array(
          "code" => "63690",
          "name" => "SALENTO",
        ),
      ),
    ),
    "RISARALDA" => array(
      "abbr" => "RIS",
      "code" => "66",
      "name" => "RISARALDA",
      "cities" => array(
        "PEREIRA" => array(
          "code" => "66001",
          "name" => "PEREIRA",
        ),
        "APÍA" => array(
          "code" => "66045",
          "name" => "APÍA",
        ),
        "BALBOA" => array(
          "code" => "66075",
          "name" => "BALBOA",
        ),
        "BELÉN DE UMBRÍA" => array(
          "code" => "66088",
          "name" => "BELÉN DE UMBRÍA",
        ),
        "DOSQUEBRADAS" => array(
          "code" => "66170",
          "name" => "DOSQUEBRADAS",
        ),
        "GUÁTICA" => array(
          "code" => "66318",
          "name" => "GUÁTICA",
        ),
        "LA CELIA" => array(
          "code" => "66383",
          "name" => "LA CELIA",
        ),
        "LA VIRGINIA" => array(
          "code" => "66400",
          "name" => "LA VIRGINIA",
        ),
        "MARSELLA" => array(
          "code" => "66440",
          "name" => "MARSELLA",
        ),
        "MISTRATÓ" => array(
          "code" => "66456",
          "name" => "MISTRATÓ",
        ),
        "PUEBLO RICO" => array(
          "code" => "66572",
          "name" => "PUEBLO RICO",
        ),
        "QUINCHÍA" => array(
          "code" => "66594",
          "name" => "QUINCHÍA",
        ),
        "SANTA ROSA DE CABAL" => array(
          "code" => "66682",
          "name" => "SANTA ROSA DE CABAL",
        ),
        "SANTUARIO" => array(
          "code" => "66687",
          "name" => "SANTUARIO",
        ),
      ),
    ),
    "SANTANDER" => array(
      "abbr" => "SAN",
      "code" => "68",
      "name" => "SANTANDER",
      "cities" => array(
        "BUCARAMANGA" => array(
          "code" => "68001",
          "name" => "BUCARAMANGA",
        ),
        "AGUADA" => array(
          "code" => "68013",
          "name" => "AGUADA",
        ),
        "ALBANIA" => array(
          "code" => "68020",
          "name" => "ALBANIA",
        ),
        "ARATOCA" => array(
          "code" => "68051",
          "name" => "ARATOCA",
        ),
        "BARBOSA" => array(
          "code" => "68077",
          "name" => "BARBOSA",
        ),
        "BARICHARA" => array(
          "code" => "68079",
          "name" => "BARICHARA",
        ),
        "BARRANCABERMEJA" => array(
          "code" => "68081",
          "name" => "BARRANCABERMEJA",
        ),
        "BETULIA" => array(
          "code" => "68092",
          "name" => "BETULIA",
        ),
        "BOLÍVAR" => array(
          "code" => "68101",
          "name" => "BOLÍVAR",
        ),
        "CABRERA" => array(
          "code" => "68121",
          "name" => "CABRERA",
        ),
        "CALIFORNIA" => array(
          "code" => "68132",
          "name" => "CALIFORNIA",
        ),
        "CAPITANEJO" => array(
          "code" => "68147",
          "name" => "CAPITANEJO",
        ),
        "CARCASÍ" => array(
          "code" => "68152",
          "name" => "CARCASÍ",
        ),
        "CEPITÁ" => array(
          "code" => "68160",
          "name" => "CEPITÁ",
        ),
        "CERRITO" => array(
          "code" => "68162",
          "name" => "CERRITO",
        ),
        "CHARALÁ" => array(
          "code" => "68167",
          "name" => "CHARALÁ",
        ),
        "CHARTA" => array(
          "code" => "68169",
          "name" => "CHARTA",
        ),
        "CHIMA" => array(
          "code" => "68176",
          "name" => "CHIMA",
        ),
        "CHIPATÁ" => array(
          "code" => "68179",
          "name" => "CHIPATÁ",
        ),
        "CIMITARRA" => array(
          "code" => "68190",
          "name" => "CIMITARRA",
        ),
        "CONCEPCIÓN" => array(
          "code" => "68207",
          "name" => "CONCEPCIÓN",
        ),
        "CONFINES" => array(
          "code" => "68209",
          "name" => "CONFINES",
        ),
        "CONTRATACIÓN" => array(
          "code" => "68211",
          "name" => "CONTRATACIÓN",
        ),
        "COROMORO" => array(
          "code" => "68217",
          "name" => "COROMORO",
        ),
        "CURITÍ" => array(
          "code" => "68229",
          "name" => "CURITÍ",
        ),
        "EL CARMEN DE CHUCURI" => array(
          "code" => "68235",
          "name" => "EL CARMEN DE CHUCURI",
        ),
        "EL GUACAMAYO" => array(
          "code" => "68245",
          "name" => "EL GUACAMAYO",
        ),
        "EL PEÑÓN" => array(
          "code" => "68250",
          "name" => "EL PEÑÓN",
        ),
        "EL PLAYÓN" => array(
          "code" => "68255",
          "name" => "EL PLAYÓN",
        ),
        "ENCINO" => array(
          "code" => "68264",
          "name" => "ENCINO",
        ),
        "ENCISO" => array(
          "code" => "68266",
          "name" => "ENCISO",
        ),
        "FLORIÁN" => array(
          "code" => "68271",
          "name" => "FLORIÁN",
        ),
        "FLORIDABLANCA" => array(
          "code" => "68276",
          "name" => "FLORIDABLANCA",
        ),
        "GALÁN" => array(
          "code" => "68296",
          "name" => "GALÁN",
        ),
        "GÁMBITA" => array(
          "code" => "68298",
          "name" => "GÁMBITA",
        ),
        "GIRÓN" => array(
          "code" => "68307",
          "name" => "GIRÓN",
        ),
        "GUACA" => array(
          "code" => "68318",
          "name" => "GUACA",
        ),
        "GUADALUPE" => array(
          "code" => "68320",
          "name" => "GUADALUPE",
        ),
        "GUAPOTÁ" => array(
          "code" => "68322",
          "name" => "GUAPOTÁ",
        ),
        "GUAVATÁ" => array(
          "code" => "68324",
          "name" => "GUAVATÁ",
        ),
        "GÜEPSA" => array(
          "code" => "68327",
          "name" => "GÜEPSA",
        ),
        "HATO" => array(
          "code" => "68344",
          "name" => "HATO",
        ),
        "JESÚS MARÍA" => array(
          "code" => "68368",
          "name" => "JESÚS MARÍA",
        ),
        "JORDÁN" => array(
          "code" => "68370",
          "name" => "JORDÁN",
        ),
        "LA BELLEZA" => array(
          "code" => "68377",
          "name" => "LA BELLEZA",
        ),
        "LANDÁZURI" => array(
          "code" => "68385",
          "name" => "LANDÁZURI",
        ),
        "LA PAZ" => array(
          "code" => "68397",
          "name" => "LA PAZ",
        ),
        "LEBRIJA" => array(
          "code" => "68406",
          "name" => "LEBRIJA",
        ),
        "LOS SANTOS" => array(
          "code" => "68418",
          "name" => "LOS SANTOS",
        ),
        "MACARAVITA" => array(
          "code" => "68425",
          "name" => "MACARAVITA",
        ),
        "MÁLAGA" => array(
          "code" => "68432",
          "name" => "MÁLAGA",
        ),
        "MATANZA" => array(
          "code" => "68444",
          "name" => "MATANZA",
        ),
        "MOGOTES" => array(
          "code" => "68464",
          "name" => "MOGOTES",
        ),
        "MOLAGAVITA" => array(
          "code" => "68468",
          "name" => "MOLAGAVITA",
        ),
        "OCAMONTE" => array(
          "code" => "68498",
          "name" => "OCAMONTE",
        ),
        "OIBA" => array(
          "code" => "68500",
          "name" => "OIBA",
        ),
        "ONZAGA" => array(
          "code" => "68502",
          "name" => "ONZAGA",
        ),
        "PALMAR" => array(
          "code" => "68522",
          "name" => "PALMAR",
        ),
        "PALMAS DEL SOCORRO" => array(
          "code" => "68524",
          "name" => "PALMAS DEL SOCORRO",
        ),
        "PÁRAMO" => array(
          "code" => "68533",
          "name" => "PÁRAMO",
        ),
        "PIEDECUESTA" => array(
          "code" => "68547",
          "name" => "PIEDECUESTA",
        ),
        "PINCHOTE" => array(
          "code" => "68549",
          "name" => "PINCHOTE",
        ),
        "PUENTE NACIONAL" => array(
          "code" => "68572",
          "name" => "PUENTE NACIONAL",
        ),
        "PUERTO PARRA" => array(
          "code" => "68573",
          "name" => "PUERTO PARRA",
        ),
        "PUERTO WILCHES" => array(
          "code" => "68575",
          "name" => "PUERTO WILCHES",
        ),
        "RIONEGRO" => array(
          "code" => "68615",
          "name" => "RIONEGRO",
        ),
        "SABANA DE TORRES" => array(
          "code" => "68655",
          "name" => "SABANA DE TORRES",
        ),
        "SAN ANDRÉS" => array(
          "code" => "68669",
          "name" => "SAN ANDRÉS",
        ),
        "SAN BENITO" => array(
          "code" => "68673",
          "name" => "SAN BENITO",
        ),
        "SAN GIL" => array(
          "code" => "68679",
          "name" => "SAN GIL",
        ),
        "SAN JOAQUÍN" => array(
          "code" => "68682",
          "name" => "SAN JOAQUÍN",
        ),
        "SAN JOSÉ DE MIRANDA" => array(
          "code" => "68684",
          "name" => "SAN JOSÉ DE MIRANDA",
        ),
        "SAN MIGUEL" => array(
          "code" => "68686",
          "name" => "SAN MIGUEL",
        ),
        "SAN VICENTE DE CHUCURÍ" => array(
          "code" => "68689",
          "name" => "SAN VICENTE DE CHUCURÍ",
        ),
        "SANTA BÁRBARA" => array(
          "code" => "68705",
          "name" => "SANTA BÁRBARA",
        ),
        "SANTA HELENA DEL OPÓN" => array(
          "code" => "68720",
          "name" => "SANTA HELENA DEL OPÓN",
        ),
        "SIMACOTA" => array(
          "code" => "68745",
          "name" => "SIMACOTA",
        ),
        "SOCORRO" => array(
          "code" => "68755",
          "name" => "SOCORRO",
        ),
        "SUAITA" => array(
          "code" => "68770",
          "name" => "SUAITA",
        ),
        "SUCRE" => array(
          "code" => "68773",
          "name" => "SUCRE",
        ),
        "SURATÁ" => array(
          "code" => "68780",
          "name" => "SURATÁ",
        ),
        "TONA" => array(
          "code" => "68820",
          "name" => "TONA",
        ),
        "VALLE DE SAN JOSÉ" => array(
          "code" => "68855",
          "name" => "VALLE DE SAN JOSÉ",
        ),
        "VÉLEZ" => array(
          "code" => "68861",
          "name" => "VÉLEZ",
        ),
        "VETAS" => array(
          "code" => "68867",
          "name" => "VETAS",
        ),
        "VILLANUEVA" => array(
          "code" => "68872",
          "name" => "VILLANUEVA",
        ),
        "ZAPATOCA" => array(
          "code" => "68895",
          "name" => "ZAPATOCA",
        ),
      ),
    ),
    "SUCRE" => array(
      "abbr" => "SUC",
      "code" => "70",
      "name" => "SUCRE",
      "cities" => array(
        "SINCELEJO" => array(
          "code" => "70001",
          "name" => "SINCELEJO",
        ),
        "BUENAVISTA" => array(
          "code" => "70110",
          "name" => "BUENAVISTA",
        ),
        "CAIMITO" => array(
          "code" => "70124",
          "name" => "CAIMITO",
        ),
        "COLOSÓ" => array(
          "code" => "70204",
          "name" => "COLOSÓ",
        ),
        "COROZAL" => array(
          "code" => "70215",
          "name" => "COROZAL",
        ),
        "COVEÑAS" => array(
          "code" => "70221",
          "name" => "COVEÑAS",
        ),
        "CHALÁN" => array(
          "code" => "70230",
          "name" => "CHALÁN",
        ),
        "EL ROBLE" => array(
          "code" => "70233",
          "name" => "EL ROBLE",
        ),
        "GALERAS" => array(
          "code" => "70235",
          "name" => "GALERAS",
        ),
        "GUARANDA" => array(
          "code" => "70265",
          "name" => "GUARANDA",
        ),
        "LA UNIÓN" => array(
          "code" => "70400",
          "name" => "LA UNIÓN",
        ),
        "LOS PALMITOS" => array(
          "code" => "70418",
          "name" => "LOS PALMITOS",
        ),
        "MAJAGUAL" => array(
          "code" => "70429",
          "name" => "MAJAGUAL",
        ),
        "MORROA" => array(
          "code" => "70473",
          "name" => "MORROA",
        ),
        "OVEJAS" => array(
          "code" => "70508",
          "name" => "OVEJAS",
        ),
        "PALMITO" => array(
          "code" => "70523",
          "name" => "PALMITO",
        ),
        "SAMPUÉS" => array(
          "code" => "70670",
          "name" => "SAMPUÉS",
        ),
        "SAN BENITO ABAD" => array(
          "code" => "70678",
          "name" => "SAN BENITO ABAD",
        ),
        "SAN JUAN DE BETULIA" => array(
          "code" => "70702",
          "name" => "SAN JUAN DE BETULIA",
        ),
        "SAN MARCOS" => array(
          "code" => "70708",
          "name" => "SAN MARCOS",
        ),
        "SAN ONOFRE" => array(
          "code" => "70713",
          "name" => "SAN ONOFRE",
        ),
        "SAN PEDRO" => array(
          "code" => "70717",
          "name" => "SAN PEDRO",
        ),
        "SAN LUIS DE SINCÉ" => array(
          "code" => "70742",
          "name" => "SAN LUIS DE SINCÉ",
        ),
        "SUCRE" => array(
          "code" => "70771",
          "name" => "SUCRE",
        ),
        "SANTIAGO DE TOLÚ" => array(
          "code" => "70820",
          "name" => "SANTIAGO DE TOLÚ",
        ),
        "SAN JOSÉ DE TOLUVIEJO" => array(
          "code" => "70823",
          "name" => "SAN JOSÉ DE TOLUVIEJO",
        ),
      ),
    ),
    "TOLIMA" => array(
      "abbr" => "TOL",
      "code" => "73",
      "name" => "TOLIMA",
      "cities" => array(
        "IBAGUÉ" => array(
          "code" => "73001",
          "name" => "IBAGUÉ",
        ),
        "ALPUJARRA" => array(
          "code" => "73024",
          "name" => "ALPUJARRA",
        ),
        "ALVARADO" => array(
          "code" => "73026",
          "name" => "ALVARADO",
        ),
        "AMBALEMA" => array(
          "code" => "73030",
          "name" => "AMBALEMA",
        ),
        "ANZOÁTEGUI" => array(
          "code" => "73043",
          "name" => "ANZOÁTEGUI",
        ),
        "ARMERO" => array(
          "code" => "73055",
          "name" => "ARMERO",
        ),
        "ATACO" => array(
          "code" => "73067",
          "name" => "ATACO",
        ),
        "CAJAMARCA" => array(
          "code" => "73124",
          "name" => "CAJAMARCA",
        ),
        "CARMEN DE APICALÁ" => array(
          "code" => "73148",
          "name" => "CARMEN DE APICALÁ",
        ),
        "CASABIANCA" => array(
          "code" => "73152",
          "name" => "CASABIANCA",
        ),
        "CHAPARRAL" => array(
          "code" => "73168",
          "name" => "CHAPARRAL",
        ),
        "COELLO" => array(
          "code" => "73200",
          "name" => "COELLO",
        ),
        "COYAIMA" => array(
          "code" => "73217",
          "name" => "COYAIMA",
        ),
        "CUNDAY" => array(
          "code" => "73226",
          "name" => "CUNDAY",
        ),
        "DOLORES" => array(
          "code" => "73236",
          "name" => "DOLORES",
        ),
        "ESPINAL" => array(
          "code" => "73268",
          "name" => "ESPINAL",
        ),
        "FALAN" => array(
          "code" => "73270",
          "name" => "FALAN",
        ),
        "FLANDES" => array(
          "code" => "73275",
          "name" => "FLANDES",
        ),
        "FRESNO" => array(
          "code" => "73283",
          "name" => "FRESNO",
        ),
        "GUAMO" => array(
          "code" => "73319",
          "name" => "GUAMO",
        ),
        "HERVEO" => array(
          "code" => "73347",
          "name" => "HERVEO",
        ),
        "HONDA" => array(
          "code" => "73349",
          "name" => "HONDA",
        ),
        "ICONONZO" => array(
          "code" => "73352",
          "name" => "ICONONZO",
        ),
        "LÉRIDA" => array(
          "code" => "73408",
          "name" => "LÉRIDA",
        ),
        "LÍBANO" => array(
          "code" => "73411",
          "name" => "LÍBANO",
        ),
        "SAN SEBASTIÁN DE MARIQUITA" => array(
          "code" => "73443",
          "name" => "SAN SEBASTIÁN DE MARIQUITA",
        ),
        "MELGAR" => array(
          "code" => "73449",
          "name" => "MELGAR",
        ),
        "MURILLO" => array(
          "code" => "73461",
          "name" => "MURILLO",
        ),
        "NATAGAIMA" => array(
          "code" => "73483",
          "name" => "NATAGAIMA",
        ),
        "ORTEGA" => array(
          "code" => "73504",
          "name" => "ORTEGA",
        ),
        "PALOCABILDO" => array(
          "code" => "73520",
          "name" => "PALOCABILDO",
        ),
        "PIEDRAS" => array(
          "code" => "73547",
          "name" => "PIEDRAS",
        ),
        "PLANADAS" => array(
          "code" => "73555",
          "name" => "PLANADAS",
        ),
        "PRADO" => array(
          "code" => "73563",
          "name" => "PRADO",
        ),
        "PURIFICACIÓN" => array(
          "code" => "73585",
          "name" => "PURIFICACIÓN",
        ),
        "RIOBLANCO" => array(
          "code" => "73616",
          "name" => "RIOBLANCO",
        ),
        "RONCESVALLES" => array(
          "code" => "73622",
          "name" => "RONCESVALLES",
        ),
        "ROVIRA" => array(
          "code" => "73624",
          "name" => "ROVIRA",
        ),
        "SALDAÑA" => array(
          "code" => "73671",
          "name" => "SALDAÑA",
        ),
        "SAN ANTONIO" => array(
          "code" => "73675",
          "name" => "SAN ANTONIO",
        ),
        "SAN LUIS" => array(
          "code" => "73678",
          "name" => "SAN LUIS",
        ),
        "SANTA ISABEL" => array(
          "code" => "73686",
          "name" => "SANTA ISABEL",
        ),
      ),
    ),
    "AMAZONAS" => array(
      "abbr" => "AMZ",
      "code" => "91",
      "name" => "AMAZONAS",
      "cities" => array(
        "LETICIA" => array(
          "code" => "91001",
          "name" => "LETICIA",
        ),
        "EL ENCANTO" => array(
          "code" => "91263",
          "name" => "EL ENCANTO",
        ),
        "LA CHORRERA" => array(
          "code" => "91405",
          "name" => "LA CHORRERA",
        ),
        "LA PEDRERA" => array(
          "code" => "91407",
          "name" => "LA PEDRERA",
        ),
        "LA VICTORIA" => array(
          "code" => "91430",
          "name" => "LA VICTORIA",
        ),
        "MIRITÍ - PARANÁ" => array(
          "code" => "91460",
          "name" => "MIRITÍ - PARANÁ",
        ),
        "PUERTO ALEGRÍA" => array(
          "code" => "91530",
          "name" => "PUERTO ALEGRÍA",
        ),
        "PUERTO ARICA" => array(
          "code" => "91536",
          "name" => "PUERTO ARICA",
        ),
        "PUERTO NARIÑO" => array(
          "code" => "91540",
          "name" => "PUERTO NARIÑO",
        ),
        "PUERTO SANTANDER" => array(
          "code" => "91669",
          "name" => "PUERTO SANTANDER",
        ),
        "TARAPACÁ" => array(
          "code" => "91798",
          "name" => "TARAPACÁ",
        ),
      ),
    ),
    "ARAUCA" => array(
      "abbr" => "ARU",
      "code" => "81",
      "name" => "ARAUCA",
      "cities" => array(
        "ARAUCA" => array(
          "code" => "81001",
          "name" => "ARAUCA",
        ),
        "ARAUQUITA" => array(
          "code" => "81065",
          "name" => "ARAUQUITA",
        ),
        "CRAVO NORTE" => array(
          "code" => "81220",
          "name" => "CRAVO NORTE",
        ),
        "FORTUL" => array(
          "code" => "81300",
          "name" => "FORTUL",
        ),
        "PUERTO RONDÓN" => array(
          "code" => "81591",
          "name" => "PUERTO RONDÓN",
        ),
        "SARAVENA" => array(
          "code" => "81736",
          "name" => "SARAVENA",
        ),
        "TAME" => array(
          "code" => "81794",
          "name" => "TAME",
        ),
      ),
    ),
    "CASANARE" => array(
      "abbr" => "CAS",
      "code" => "85",
      "name" => "CASANARE",
      "cities" => array(
        "YOPAL" => array(
          "code" => "85001",
          "name" => "YOPAL",
        ),
        "AGUAZUL" => array(
          "code" => "85010",
          "name" => "AGUAZUL",
        ),
        "CHÁMEZA" => array(
          "code" => "85015",
          "name" => "CHÁMEZA",
        ),
        "HATO COROZAL" => array(
          "code" => "85125",
          "name" => "HATO COROZAL",
        ),
        "LA SALINA" => array(
          "code" => "85136",
          "name" => "LA SALINA",
        ),
        "MANÍ" => array(
          "code" => "85139",
          "name" => "MANÍ",
        ),
        "MONTERREY" => array(
          "code" => "85162",
          "name" => "MONTERREY",
        ),
        "NUNCHÍA" => array(
          "code" => "85225",
          "name" => "NUNCHÍA",
        ),
        "OROCUÉ" => array(
          "code" => "85230",
          "name" => "OROCUÉ",
        ),
        "PAZ DE ARIPORO" => array(
          "code" => "85250",
          "name" => "PAZ DE ARIPORO",
        ),
        "PORE" => array(
          "code" => "85263",
          "name" => "PORE",
        ),
        "RECETOR" => array(
          "code" => "85279",
          "name" => "RECETOR",
        ),
        "SABANALARGA" => array(
          "code" => "85300",
          "name" => "SABANALARGA",
        ),
        "SÁCAMA" => array(
          "code" => "85315",
          "name" => "SÁCAMA",
        ),
        "SAN LUIS DE PALENQUE" => array(
          "code" => "85325",
          "name" => "SAN LUIS DE PALENQUE",
        ),
        "TÁMARA" => array(
          "code" => "85400",
          "name" => "TÁMARA",
        ),
        "TAURAMENA" => array(
          "code" => "85410",
          "name" => "TAURAMENA",
        ),
        "TRINIDAD" => array(
          "code" => "85430",
          "name" => "TRINIDAD",
        ),
        "VILLANUEVA" => array(
          "code" => "85440",
          "name" => "VILLANUEVA",
        ),
      ),
    ),
    "GUAINÍA" => array(
      "abbr" => "GUA",
      "code" => "94",
      "name" => "GUAINÍA",
      "cities" => array(
        "INÍRIDA" => array(
          "code" => "94001",
          "name" => "INÍRIDA",
        ),
        "BARRANCOMINAS" => array(
          "code" => "94343",
          "name" => "BARRANCOMINAS",
        ),
        "SAN FELIPE" => array(
          "code" => "94883",
          "name" => "SAN FELIPE",
        ),
        "PUERTO COLOMBIA" => array(
          "code" => "94884",
          "name" => "PUERTO COLOMBIA",
        ),
        "LA GUADALUPE" => array(
          "code" => "94885",
          "name" => "LA GUADALUPE",
        ),
        "CACAHUAL" => array(
          "code" => "94886",
          "name" => "CACAHUAL",
        ),
        "PANA PANA" => array(
          "code" => "94887",
          "name" => "PANA PANA",
        ),
        "MORICHAL" => array(
          "code" => "94888",
          "name" => "MORICHAL",
        ),
      ),
    ),
    "GUAVIARE" => array(
      "abbr" => "GUV",
      "code" => "95",
      "name" => "GUAVIARE",
      "cities" => array(
        "SAN JOSÉ DEL GUAVIARE" => array(
          "code" => "95001",
          "name" => "SAN JOSÉ DEL GUAVIARE",
        ),
        "CALAMAR" => array(
          "code" => "95015",
          "name" => "CALAMAR",
        ),
        "EL RETORNO" => array(
          "code" => "95025",
          "name" => "EL RETORNO",
        ),
        "MIRAFLORES" => array(
          "code" => "95200",
          "name" => "MIRAFLORES",
        ),
      ),
    ),
    "PUTUMAYO" => array(
      "abbr" => "PUT",
      "code" => "86",
      "name" => "PUTUMAYO",
      "cities" => array(
        "MOCOA" => array(
          "code" => "86001",
          "name" => "MOCOA",
        ),
        "COLÓN" => array(
          "code" => "86219",
          "name" => "COLÓN",
        ),
        "ORITO" => array(
          "code" => "86320",
          "name" => "ORITO",
        ),
        "PUERTO ASÍS" => array(
          "code" => "86568",
          "name" => "PUERTO ASÍS",
        ),
        "PUERTO CAICEDO" => array(
          "code" => "86569",
          "name" => "PUERTO CAICEDO",
        ),
        "PUERTO GUZMÁN" => array(
          "code" => "86571",
          "name" => "PUERTO GUZMÁN",
        ),
        "PUERTO LEGUÍZAMO" => array(
          "code" => "86573",
          "name" => "PUERTO LEGUÍZAMO",
        ),
        "SIBUNDOY" => array(
          "code" => "86749",
          "name" => "SIBUNDOY",
        ),
        "SAN FRANCISCO" => array(
          "code" => "86755",
          "name" => "SAN FRANCISCO",
        ),
        "SAN MIGUEL" => array(
          "code" => "86757",
          "name" => "SAN MIGUEL",
        ),
        "SANTIAGO" => array(
          "code" => "86760",
          "name" => "SANTIAGO",
        ),
        "VALLE DEL GUAMUEZ" => array(
          "code" => "86865",
          "name" => "VALLE DEL GUAMUEZ",
        ),
        "VILLAGARZÓN" => array(
          "code" => "86885",
          "name" => "VILLAGARZÓN",
        ),
      ),
    ),
    "ARCHIPIÉLAGO DE SAN ANDRÉS. PROVIDENCIA Y SANTA CATALINA" => array(
      "abbr" => "SAP",
      "code" => "88",
      "name" => "ARCHIPIÉLAGO DE SAN ANDRÉS. PROVIDENCIA Y SANTA CATALINA",
      "cities" => array(
        "SAN ANDRÉS" => array(
          "code" => "88001",
          "name" => "SAN ANDRÉS",
        ),
        "PROVIDENCIA" => array(
          "code" => "88564",
          "name" => "PROVIDENCIA",
        ),
      ),
    ),
    "VALLE DEL CAUCA" => array(
      "abbr" => "VAC",
      "code" => "76",
      "name" => "VALLE DEL CAUCA",
      "cities" => array(
        "CALI" => array(
          "code" => "76001",
          "name" => "CALI",
        ),
        "ALCALÁ" => array(
          "code" => "76020",
          "name" => "ALCALÁ",
        ),
        "ANDALUCÍA" => array(
          "code" => "76036",
          "name" => "ANDALUCÍA",
        ),
        "ANSERMANUEVO" => array(
          "code" => "76041",
          "name" => "ANSERMANUEVO",
        ),
        "ARGELIA" => array(
          "code" => "76054",
          "name" => "ARGELIA",
        ),
        "BOLÍVAR" => array(
          "code" => "76100",
          "name" => "BOLÍVAR",
        ),
        "BUENAVENTURA" => array(
          "code" => "76109",
          "name" => "BUENAVENTURA",
        ),
        "GUADALAJARA DE BUGA" => array(
          "code" => "76111",
          "name" => "GUADALAJARA DE BUGA",
        ),
        "BUGALAGRANDE" => array(
          "code" => "76113",
          "name" => "BUGALAGRANDE",
        ),
        "CAICEDONIA" => array(
          "code" => "76122",
          "name" => "CAICEDONIA",
        ),
        "CALIMA" => array(
          "code" => "76126",
          "name" => "CALIMA",
        ),
        "CANDELARIA" => array(
          "code" => "76130",
          "name" => "CANDELARIA",
        ),
        "CARTAGO" => array(
          "code" => "76147",
          "name" => "CARTAGO",
        ),
        "DAGUA" => array(
          "code" => "76233",
          "name" => "DAGUA",
        ),
        "EL ÁGUILA" => array(
          "code" => "76243",
          "name" => "EL ÁGUILA",
        ),
        "EL CAIRO" => array(
          "code" => "76246",
          "name" => "EL CAIRO",
        ),
        "EL CERRITO" => array(
          "code" => "76248",
          "name" => "EL CERRITO",
        ),
        "EL DOVIO" => array(
          "code" => "76250",
          "name" => "EL DOVIO",
        ),
        "FLORIDA" => array(
          "code" => "76275",
          "name" => "FLORIDA",
        ),
        "GINEBRA" => array(
          "code" => "76306",
          "name" => "GINEBRA",
        ),
        "GUACARÍ" => array(
          "code" => "76318",
          "name" => "GUACARÍ",
        ),
        "JAMUNDÍ" => array(
          "code" => "76364",
          "name" => "JAMUNDÍ",
        ),
        "LA CUMBRE" => array(
          "code" => "76377",
          "name" => "LA CUMBRE",
        ),
        "LA UNIÓN" => array(
          "code" => "76400",
          "name" => "LA UNIÓN",
        ),
        "LA VICTORIA" => array(
          "code" => "76403",
          "name" => "LA VICTORIA",
        ),
        "OBANDO" => array(
          "code" => "76497",
          "name" => "OBANDO",
        ),
        "PALMIRA" => array(
          "code" => "76520",
          "name" => "PALMIRA",
        ),
        "PRADERA" => array(
          "code" => "76563",
          "name" => "PRADERA",
        ),
        "RESTREPO" => array(
          "code" => "76606",
          "name" => "RESTREPO",
        ),
        "RIOFRÍO" => array(
          "code" => "76616",
          "name" => "RIOFRÍO",
        ),
        "ROLDANILLO" => array(
          "code" => "76622",
          "name" => "ROLDANILLO",
        ),
        "SAN PEDRO" => array(
          "code" => "76670",
          "name" => "SAN PEDRO",
        ),
        "SEVILLA" => array(
          "code" => "76736",
          "name" => "SEVILLA",
        ),
        "TORO" => array(
          "code" => "76823",
           "name" => "TORO",
        ),
        "TRUJILLO" => array(
          "code" => "76828",
          "name" => "TRUJILLO",
        ),
        "TULUÁ" => array(
          "code" => "76834",
          "name" => "TULUÁ",
        ),
        "ULLOA" => array(
          "code" => "76845",
          "name" => "ULLOA",
        ),
        "VERSALLES" => array(
          "code" => "76863",
          "name" => "VERSALLES",
        ),
        "VIJES" => array(
          "code" => "76869",
          "name" => "VIJES",
        ),
        "YOTOCO" => array(
          "code" => "76890",
          "name" => "YOTOCO",
        ),
        "YUMBO" => array(
          "code" => "76892",
          "name" => "YUMBO",
        ),
        "ZARZAL" => array(
          "code" => "76895",
          "name" => "ZARZAL",
        ),
      ),
    ),
    "VAUPÉS" => array(
      "abbr" => "VAU",
      "code" => "97",
      "name" => "VAUPÉS",
      "cities" => array(
        "MITÚ" => array(
          "code" => "97001",
          "name" => "MITÚ",
        ),
        "CARURÚ" => array(
          "code" => "97161",
          "name" => "CARURÚ",
        ),
        "PACOA" => array(
          "code" => "97511",
          "name" => "PACOA",
        ),
        "TARAIRA" => array(
          "code" => "97666",
          "name" => "TARAIRA",
        ),
        "PAPUNAHUA" => array(
          "code" => "97777",
          "name" => "PAPUNAHUA",
        ),
        "YAVARATÉ" => array(
          "code" => "97889",
          "name" => "YAVARATÉ",
        ),
      ),
    ),
    "VICHADA" => array(
      "abbr" => "VIC",
      "code" => "99",
      "name" => "VICHADA",
      "cities" => array(
        "PUERTO CARREÑO" => array(
          "code" => "99001",
          "name" => "PUERTO CARREÑO",
        ),
        "LA PRIMAVERA" => array(
          "code" => "99524",
          "name" => "LA PRIMAVERA",
        ),
        "SANTA ROSALÍA" => array(
          "code" => "99624",
          "name" => "SANTA ROSALÍA",
        ),
        "CUMARIBO" => array(
          "code" => "99773",
          "name" => "CUMARIBO",
        ),
      ),
    ),
  );
}

/**
 * Devuelve la lista de localidades de Bogotá.
 * @return array
 */
function wrs_get_bogota_localidades() {
    return array(
        '' => __('Selecciona una Localidad...', 'woodmart-referral-system'), // Traducible
        'USAQUEN' => 'Usaqu&eacute;n', 'CHAPINERO' => 'Chapinero', 'SANTA_FE' => 'Santa Fe',
        'SAN_CRISTOBAL' => 'San Crist&oacute;bal', 'USME' => 'Usme', 'TUNJUELITO' => 'Tunjuelito', 'BOSA' => 'Bosa',
        'KENNEDY' => 'Kennedy', 'FONTIBON' => 'Fontib&oacute;n', 'ENGATIVA' => 'Engativ&aacute;', 'SUBA' => 'Suba',
        'BARRIOS_UNIDOS' => 'Barrios Unidos', 'TEUSAQUILLO' => 'Teusaquillo', 'LOS_MARTIRES' => 'Los M&aacute;rtires',
        'ANTONIO_NARINO' => 'Antonio Nari&ntilde;o', 'PUENTE_ARANDA' => 'Puente Aranda', 'LA_CANDELARIA' => 'La Candelaria',
        'RAFAEL_URIBE_URIBE' => 'Rafael Uribe Uribe', 'CIUDAD_BOLIVAR' => 'Ciudad Bol&iacute;var', 'SUMAPAZ' => 'Sumapaz'
    );
}

/**
 * Devuelve arrays de opciones para campos selectores del formulario.
 * @param string $list_name Nombre de la lista de opciones a obtener.
 * @return array
 */
function wrs_get_options($list_name) {
    $options = array();
    // Claves sin tildes/simplificadas, Texto con entidades HTML y traducibles
    switch ($list_name) {
        case 'tipo_doc': 
            $options = array('' => __('Selecciona...', 'woodmart-referral-system'), 'CC' => __('C&eacute;dula Ciudadan&iacute;a', 'woodmart-referral-system'), 'CE' => __('C&eacute;dula Extranjer&iacute;a', 'woodmart-referral-system'), 'PA' => __('Pasaporte', 'woodmart-referral-system'), 'TI' => __('Tarjeta Identidad', 'woodmart-referral-system'), 'RC' => __('Registro Civil', 'woodmart-referral-system'), 'NIT' => __('NIT', 'woodmart-referral-system'), 'OTRO' => __('Otro', 'woodmart-referral-system')); 
            break;
        case 'genero': 
            $options = array('' => __('Selecciona...', 'woodmart-referral-system'), 'FEMENINO' => __('Femenino', 'woodmart-referral-system'), 'MASCULINO' => __('Masculino', 'woodmart-referral-system'), 'NO_BINARIO' => __('No binario', 'woodmart-referral-system'), 'OTRO' => __('Otro', 'woodmart-referral-system'), 'PREFIERO_NO_INDICAR' => __('Prefiero no indicar', 'woodmart-referral-system')); 
            break;
        case 'estado_civil': 
            $options = array('' => __('Selecciona...', 'woodmart-referral-system'), 'SOLTERO' => __('Soltero(a)', 'woodmart-referral-system'), 'CASADO' => __('Casado(a)', 'woodmart-referral-system'), 'UNION_LIBRE' => __('Uni&oacute;n Libre', 'woodmart-referral-system'), 'SEPARADO' => __('Separado(a)', 'woodmart-referral-system'), 'DIVORCIADO' => __('Divorciado(a)', 'woodmart-referral-system'), 'VIUDO' => __('Viudo(a)', 'woodmart-referral-system')); 
            break;
        case 'nivel_educativo': 
            $options = array('' => __('Selecciona...', 'woodmart-referral-system'), 'SIN_ESTUDIOS' => __('Sin estudios', 'woodmart-referral-system'), 'PRIMARIA' => __('Primaria', 'woodmart-referral-system'), 'BACHILLERATO' => __('Bachillerato', 'woodmart-referral-system'), 'TECNICO' => __('T&eacute;cnico', 'woodmart-referral-system'), 'TECNOLOGO' => __('Tecn&oacute;logo', 'woodmart-referral-system'), 'PROFESIONAL' => __('Profesional', 'woodmart-referral-system'), 'ESPECIALIZACION' => __('Especializaci&oacute;n', 'woodmart-referral-system'), 'MAESTRIA' => __('Maestr&iacute;a', 'woodmart-referral-system'), 'DOCTORADO' => __('Doctorado', 'woodmart-referral-system')); 
            break;
        case 'ocupacion': 
            $options = array('' => __('Selecciona...', 'woodmart-referral-system'), 'EMPLEADO' => __('Empleado(a)', 'woodmart-referral-system'), 'INDEPENDIENTE' => __('Independiente/Aut&oacute;nomo', 'woodmart-referral-system'), 'ESTUDIANTE' => __('Estudiante', 'woodmart-referral-system'), 'HOGAR' => __('Hogar', 'woodmart-referral-system'), 'PENSIONADO' => __('Pensionado(a)', 'woodmart-referral-system'), 'DESEMPLEADO' => __('Desempleado(a)', 'woodmart-referral-system'), 'BUSCA_EMPLEO' => __('Busca empleo', 'woodmart-referral-system'), 'OTRO' => __('Otro', 'woodmart-referral-system')); 
            break;
        case 'es_cuidador': 
            $options = array('' => __('Selecciona...', 'woodmart-referral-system'), 'SI' => __('S&iacute;', 'woodmart-referral-system'), 'NO' => __('No', 'woodmart-referral-system')); 
            break;
        case 'temas_interes': 
            $options = array('POLITICA' => __('Pol&iacute;tica', 'woodmart-referral-system'), 'DEPORTES' => __('Deportes', 'woodmart-referral-system'), 'ECONOMIA' => __('Econom&iacute;a', 'woodmart-referral-system'), 'ACTUALIDAD' => __('Actualidad', 'woodmart-referral-system'), 'SOCIALES' => __('Sociales', 'woodmart-referral-system'), 'TECNOLOGIA' => __('Tecnolog&iacute;a', 'woodmart-referral-system'), 'SALUD' => __('Salud', 'woodmart-referral-system'), 'EDUCACION' => __('Educaci&oacute;n', 'woodmart-referral-system'), 'MEDIO_AMBIENTE' => __('Medio Ambiente', 'woodmart-referral-system'), 'BIENESTAR_ANIMAL' => __('Bienestar Animal', 'woodmart-referral-system'), 'CULTURA' => __('Cultura', 'woodmart-referral-system'), 'MOVILIDAD' => __('Movilidad', 'woodmart-referral-system'), 'SEGURIDAD' => __('Seguridad', 'woodmart-referral-system'), 'OTRO' => __('Otro', 'woodmart-referral-system')); 
            break;
    }
    return $options;
}

/**
 * Obtiene todos los IDs de los descendientes de un conjunto dado de IDs de usuarios padres.
 */
function wrs_get_all_descendants_ids($parent_user_ids, $max_depth = 20) {
    $all_descendants_ids = array();
    $current_level_parent_ids = (array) $parent_user_ids; 
    $processed_ids = array_fill_keys($current_level_parent_ids, true); 

    for ($depth = 0; $depth < $max_depth && !empty($current_level_parent_ids); $depth++) {
        $args = array(
            'meta_key'     => '_wrs_referrer_id',
            'meta_value'   => $current_level_parent_ids,
            'meta_compare' => 'IN',
            'fields'       => 'ID',
            'number'       => -1, 
        );
        $direct_children_ids_query = new WP_User_Query($args);
        $direct_children_ids = $direct_children_ids_query->get_results();

        if (empty($direct_children_ids)) {
            break; 
        }

        $newly_found_children_ids = array();
        foreach ($direct_children_ids as $child_id) {
            if (!isset($processed_ids[$child_id])) { 
                $newly_found_children_ids[] = $child_id;
                $all_descendants_ids[] = $child_id;
                $processed_ids[$child_id] = true;
            }
        }
        
        if (empty($newly_found_children_ids)) {
            break; 
        }
        $current_level_parent_ids = $newly_found_children_ids; 
    }
    return array_unique($all_descendants_ids);
}

// NO AÑADIR MÁS FUNCIONES AQUÍ. ESTE ARCHIVO ES SOLO PARA LAS FUNCIONES DE ARRIBA.
?>
<?php

namespace App\Entity;


class Departments 
{
	
	private const TAB_DEPARTENT = 
	[
		'01' => 'Ain',
		'02' => 'Aisine',
		'03' => 'Allier',
		'04' => 'Alpes-de-Haute-Provence',
		'05' => 'Hautes-Alpes',
		'06' => 'Alpes-Maritimes',
		'07' => 'Ardèche',
		'08' => 'Ardennes',
		'09' => 'Ariège',
		'10' => 'Aube',
		'11' => 'Aude',
		'12' => 'Aveyron',
		'13' => 'Bouches-du-Rhône',
		'14' => 'Calvados',
		'15' => 'Cantal',
		'16' => 'Charente',
		'17' => 'Charente-Maritime',
		'18' => 'Cher',
		'19' => 'Corrèze',
		'20' => ['1' => 'Corse-du-Sud','2' => 'Haute-Corse'],
		'21' => "Côte-d'or",
		'22' => "Côte-d'Armor",
		'23' => 'Creuse',
		'24' => 'Dordogne',
		'25' => 'Doubs',
		'26' => 'Drôme',
		'27' => 'Eure',
		'28' => 'Eure-et-Loir',
		'29' => 'Finistère',
		'30' => 'Gard',
		'31' => 'Haute-Garonne',
		'32' => 'Gers',
		'33' => 'Gironde',
		'34' => 'Hérault',
		'35' => 'Ille-et-Vilaine',
		'36' => 'Indre',
		'37' => 'Indre-et-Loire',
		'38' => 'Isère',
		'39' => 'Jura',
		'40' => 'Landes',
		'41' => 'Loir-et-Cher',
		'42' => 'Loire',
		'43' => 'Haute-Loire',
		'44' => 'Loire-Atlantique',
		'45' => 'Loiret',
		'46' => 'Lot',
		'47' => 'Lot-et-Garonne',
		'48' => 'Lozère',
		'49' => 'Maine-et-Loire',
		'50' => 'Manche',
		'51' => 'Marne',
		'52' => 'Haute-Marne',
		'53' => 'Mayenne',
		'54' => 'Meurthe-et-Moselle',
		'55' => 'Meuse',
		'56' => 'Morbihan',
		'57' => 'Moselle',
		'58' => 'Nièvre',
		'59' => 'Nord',
		'60' => 'Oise',
		'61' => 'Orne',
		'62' => 'Pas-de-Calais',
		'63' => 'Puy-de-Dôme',
		'64' => 'Pyrénées-Atlantiques',
		'65' => 'Hautes-Pyrénées',
		'66' => 'Pyrénées-Orientales',
		'67' => 'Bas-Rhin',
		'68' => 'Haut-Rhin',
		'69' => 'Rhône',
		'70' => 'Haute-Saône',
		'71' => 'Saône-et-Loire',
		'72' => 'Sarthe',
		'73' => 'Savoie',
		'74' => 'Haute-Savoie',
		'75' => 'Paris',
		'76' => 'Seine-Maritime',
		'77' => 'Seine-et-Marne',
		'78' => 'Yvelines',
		'79' => 'Deux-Sèvres',
		'80' => 'Somme',
		'81' => 'Tarn',
		'82' => 'Tarn-et-Garonne',
		'83' => 'Var',
		'84' => 'Vaucluse',
		'85' => 'Vendée',
		'86' => 'Vienne',
		'87' => 'Haute-Vienne',
		'88' => 'Vosges',
		'89' => 'yonne',
		'90' => 'Territoire de Belfort',
		'91' => 'Essonne',
		'92' => 'Hautes-de-Seine',
		'93' => 'Seine-Saint-Denis',
		'94' => 'Val-de-Marne',
		'95' => "Val-d'oise",
		'97' => ['1' => 'Guadeloupe', '2' => 'Martinique', '3' => 'Guyane', '4' => 'La Réunion', '6' => 'Mayotte']
	];

	public function getNameByZipCode(string $zip_code)
	{
		$key = substr($zip_code, 0, 2);
		if(array_key_exists($key, self::TAB_DEPARTENT)) {
			$val = self::TAB_DEPARTENT[$key];
			if(gettype($val) === "string")
				return $val;
			else {
				$key = substr($zip_code, 3, 1);
				if(array_key_exists($key, $val))
					return $val[$key];
			}
		}
		return "Le code postale ne correspond pas à un département. de correspond pas.";
	}

	public function getListDepartment() 
	{
		$tab = array();
		foreach(self::TAB_DEPARTENT as $key => $value) {
			if(gettype($value) === "string")
				$tab[$key] = $value;
			else {
				foreach($value as $second_key => $second_value)
					$tab[$key.$second_key] = $second_value;
			}
		}
		return $tab;
	}

}

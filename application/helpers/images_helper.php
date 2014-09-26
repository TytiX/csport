<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CSPORT
 * refereeing and matches management tool
 *
 * Copyright (c) 2012 Marie Kuntz - Lezard Rouge
 *
 * This file is part of CSPORT.
 * CSPORT is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * CSPORT is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with CSPORT.  If not, see <http://www.gnu.org/licenses/>.
 * See LICENCE.TXT file for more information.
 *
 * @copyright  Copyright (c) 2013-2014 Marie Kuntz - Lezard Rouge (http://www.lezard-rouge.fr)
 * @license    GNU-GPL v3 http://www.gnu.org/licenses/gpl.html
 * @version    1.0
 * @author     Marie Kuntz - Lezard Rouge SARL - www.lezard-rouge.fr - info@lezard-rouge.fr
 */

// ------------------------------------------------------------------------

/**
 * Images Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 *
 * @version $Id: images_helper.php 85 2014-08-27 10:18:51Z lezardro $
 * @author  Marie Kuntz / Lezard Rouge
 */

// ------------------------------------------------------------------------

function _img_add_props()
{
	return array(
		'src' => CRH_PATH_TO_IMG . 'add.png',
		'alt' => '+',
		'class' => 'icon',
		'title' => 'Nouveau'
	);
}

// ------------------------------------------------------------------------

function _img_edit_props()
{
	return array(
		'src' => CRH_PATH_TO_IMG . 'edit.png',
		'class' => 'icon',
		'alt' => 'Modifier',
		'title' => 'Modifier'
	);
}

// ------------------------------------------------------------------------

function _img_del_props($alt = 'Supprimer')
{
	return array(
		'src'   => CRH_PATH_TO_IMG . 'del.png',
		'class' => 'icon',
		'alt'   => $alt,
		'title' => $alt
	);
}

// ------------------------------------------------------------------------

function _img_user_props()
{
	return array(
		'src' => CRH_PATH_TO_IMG . 'user.png',
		'alt' => 'Utilisateurs',
		'class' => 'icon',
		'title' => 'Utilisateurs'
	);
}


// ------------------------------------------------------------------------


function _img_details_props($id = '')
{
	return array(
		'src' => CRH_PATH_TO_IMG . 'detail.png',
		'class' => 'icon',
		'alt' => 'Détails',
		'title' => 'Détails'
	);
}


// ------------------------------------------------------------------------

function _img_more_props($id = '')
{
	$array = array(
		'src' => CRH_PATH_TO_IMG . 'detail.png',
		'class' => 'icon more',
		'alt' => 'Détails',
		'title' => 'Détails'
	);
	if( ! empty($id)) {
		$array['id'] = $id;
	}
	 return $array;
}


// ------------------------------------------------------------------------

function _img_back_props()
{
	return array(
		'src' => CRH_PATH_TO_IMG . 'back.png',
		'alt' => '&lt;&lt;',
		'class' => 'icon',
		'title' => 'Retour'
	);
}


// ------------------------------------------------------------------------

function _img_print_props()
{
	return array(
		'src' => CRH_PATH_TO_IMG . 'printer.png',
		'alt' => 'imprimer',
		'class' => 'icon',
		'title' => 'imprimer'
	);
}

// ------------------------------------------------------------------------

function _img_tip_props($title = 'Plus d\'informations')
{
	return array(
		'src' => CRH_PATH_TO_IMG . 'information.png',
		'alt' => $title,
		'class' => 'tip',
		'title' => $title
	);
}

// ------------------------------------------------------------------------

function _img_order_desc_props()
{
	return '<span class="glyphicon glyphicon-chevron-down"></span>';
	/*return img(array(
		'src' => CRH_PATH_TO_IMG . 'order-desc.png',
		'class' => 'icon-desc',
		'alt' => 'ordre décroissant',
		'title' => 'ordre décroissant'
	));*/
}


// ------------------------------------------------------------------------

function _img_order_asc_props()
{
	return '<span class="glyphicon glyphicon-chevron-up"></span>';
	/*
	return img(array(
		'src' => CRH_PATH_TO_IMG . 'order-asc.png',
		'class' => 'icon-asc',
		'alt' => 'ordre croissant',
		'title' => 'ordre croissant'
	));*/
}


// ------------------------------------------------------------------------

function _img_pwd_props($alt = 'Changer le mot de passe')
{
	$array = array(
		'src' => CRH_PATH_TO_IMG . 'key.png',
		'class' => 'icon',
		'alt' => $alt,
		'title' => $alt
	);
	return $array;
}

// ------------------------------------------------------------------------

function _img_logout_props()
{
	return array(
		'src' => CRH_PATH_TO_IMG . 'logout.png',
		'alt' => 'Déconnexion',
		'class' => 'icon',
		'title' => 'Déconnexion'
	);
}


// ------------------------------------------------------------------------

function _img_active_props()
{
	return array(
		'src' => CRH_PATH_TO_IMG . 'active.png',
		'alt' => 'Actif',
		'class' => 'icon',
		'title' => 'Actif'
	);
}

// ------------------------------------------------------------------------

function _img_inactive_props()
{
	return array(
		'src' => CRH_PATH_TO_IMG . 'inactive.png',
		'alt' => 'Inactif',
		'class' => 'icon',
		'title' => 'Inactif'
	);
}

// ------------------------------------------------------------------------

function _img_search_props()
{
	return array(
		'src' => CRH_PATH_TO_IMG . 'search.png',
		'alt' => 'Chercher',
		'class' => 'icon',
		'title' => 'Chercher'
	);
}

// ------------------------------------------------------------------------

function _img_file_upload_props()
{
	return array(
		'src' => CRH_PATH_TO_IMG . 'file_upload.png',
		'alt' => 'Uploader un fichier',
		'class' => 'icon',
		'title' => 'Uploader un fichier'
	);
}

// ------------------------------------------------------------------------

function _img_file_download_props($title = 'Télécharger un fichier')
{
	return array(
		'src' => CRH_PATH_TO_IMG . 'file_download.png',
		'alt' => $title,
		'class' => 'icon',
		'title' => $title
	);
}

// ------------------------------------------------------------------------

function _img_generate_props($title = 'Générer')
{
	return array(
		'src' => CRH_PATH_TO_IMG . 'refresh.png',
		'alt' => $title,
		'class' => 'icon',
		'title' => $title
	);
}

// ------------------------------------------------------------------------

function _img_checked_props($title = 'Oui')
{
	return array(
		'src' => CRH_PATH_TO_IMG . 'checked.png',
		'alt' => $title,
		'class' => 'icon',
		'title' => $title
	);
}

// ------------------------------------------------------------------------

function _img_uncheck_props($title = 'Non')
{
	return array(
		'src' => CRH_PATH_TO_IMG . 'check.png',
		'alt' => $title,
		'class' => 'icon',
		'title' => $title
	);
}

// ------------------------------------------------------------------------

function _img_email_props()
{
	return array(
		'src' => CRH_PATH_TO_IMG . 'email_go.png',
		'alt' => 'Convocation',
		'class' => 'icon',
		'title' => 'Convocation'
	);
}

// ------------------------------------------------------------------------

function _img_alert_props()
{
	return array(
		'src' => CRH_PATH_TO_IMG . 'alert.png',
		'alt' => 'Alerter',
		'class' => 'icon',
		'title' => 'Alerter'
	);
}

// ------------------------------------------------------------------------

function _img_save_props()
{
	return array(
		'src' => CRH_PATH_TO_IMG . 'save.png',
		'alt' => 'Enregistrer',
		'class' => 'icon',
		'title' => 'Enregistrer'
	);
}

// ------------------------------------------------------------------------


/* End of file images_helpers.php */
/* Location: ./application/helpers/images_helpers.php */
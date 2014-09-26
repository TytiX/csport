<?php
/**
 * CSPORT
 * refereeing and matches management tool
 *
 * Copyright (c) 2013-2014 Marie Kuntz - Lezard Rouge
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

/**
 * MODEL
 * emails management
 *
 * @version $Id: emails_model.php 94 2014-09-24 11:37:03Z lezardro $
 * @author Marie Kuntz / Lezard Rouge
 *
 */

class Emails_model extends CI_Model
{

	function __construct ()
	{
		parent::__construct();
		$this->load->library('email');
	}


	/**
	 * send an notification to summon teams to a match
	 *
	 * @param object $match
	 * @param query result $users
	 *
	 * @return bool
	 */
	function sendTeamNotification($match, $users)
	{
		$subject = 'CRH Pays de la Loire : convocation d équipe';
		$this->load->model('users_model');
		$recipients = self::_formatRecipients($users);
		$referees = Misc_model::formatReferees($match);
		$data = array(
			'date'     => $match->match_date_format,
			'hour'     => $match->match_time_format,
			'place'    => $match->place_name,
			'cs'       => $match->cs_name,
			'cat'      => $match->category_name,
			'team1'    => $match->team1_name . ( ! empty($match->match_team1_status)? ' (statut : ' . $match->match_team1_status . ')':''),
			'team2'    => $match->team2_name . ( ! empty($match->match_team2_status)? ' (statut : ' . $match->match_team2_status . ')':''),
			'referees' => $referees,
		);
		$this->template->set_template('mail');
		$this->template->write_view('main_content', 'templates/mail_team_notification', $data, true);
		$message = $this->template->render('', true);
		return self::_sendMail($recipients, $subject, $message);
	}


	/**
	 * send an email to teams managers when a match has changed
	 *
	 * @param object $match
	 * @param array $users
	 *
	 * @return bool
	 */
	function sendAlertTeamMatchChanged($match, $users)
	{
		$subject = 'CRH Pays de la Loire : MODIFICATION convocation d équipe';
		$recipients = self::_formatRecipients($users);
		$referees = Misc_model::formatReferees($match);
		$data = array(
			'date'     => $match->match_date_format,
			'hour'     => $match->match_time_format,
			'place'    => $match->place_name,
			'cs'       => $match->cs_name,
			'cat'      => $match->category_name,
			'team1'    => $match->team1_name . ( ! empty($match->match_team1_status)? ' (statut : ' . $match->match_team1_status . ')':''),
			'team2'    => $match->team2_name . ( ! empty($match->match_team2_status)? ' (statut : ' . $match->match_team2_status . ')':''),
			'referees' => $referees,
		);
		$this->template->set_template('mail');
		$this->template->write_view('main_content', 'templates/mail_team_change_notification', $data, true);
		$message = $this->template->render('', true);
		return self::_sendMail($recipients, $subject, $message);
	}


	/**
	 * send an email to referees when a match has changed
	 *
	 * @param object $match
	 * @param array $users
	 *
	 * @return bool
	 */
	function sendAlertRefereeMatchChanged($match, $users)
	{
		$subject = 'CRH Pays de la Loire : MODIFICATION convocation arbitrage';
		$recipients = self::_formatRecipients($users);
		$referees = Misc_model::formatReferees($match);
		$data = array(
			'date'     => $match->match_date_format,
			'hour'     => $match->match_time_format,
			'place'    => $match->place_name,
			'cs'       => $match->cs_name,
			'cat'      => $match->category_name,
			'team1'    => $match->team1_name . ( ! empty($match->match_team1_status)? ' (statut : ' . $match->match_team1_status . ')':''),
			'team2'    => $match->team2_name . ( ! empty($match->match_team2_status)? ' (statut : ' . $match->match_team2_status . ')':''),
			'referees' => $referees,
		);
		$this->template->set_template('mail');
		$this->template->write_view('main_content', 'templates/mail_referee_change_notification', $data, true);
		$message = $this->template->render('', true);
		return self::_sendMail($recipients, $subject, $message);
	}


	/**
	 * send an alert when a team cannot play a match
	 *
	 * @param object $match
	 * @param string $status ; F|R
	 * @param query result $users
	 *
	 * @return bool
	 */
	function sendAlertTeamNok($match, $status, $users)
	{
		if($status == 'F') {
			$subject = 'CRH Pays de la Loire : annonce de forfait';
			$template = 'mail_team_cancel_notification';
		} elseif($status == 'R') {
			$subject = 'CRH Pays de la Loire : demande de report';
			$template = 'mail_team_defert_notification';
		}
		$recipients = self::_formatRecipients($users);
		$referees = Misc_model::formatReferees($match);
		$data = array(
			'date'     => $match->match_date_format,
			'hour'     => $match->match_time_format,
			'place'    => $match->place_name,
			'cs'       => $match->cs_name,
			'cat'      => $match->category_name,
			'team1'    => $match->team1_name . ( ! empty($match->match_team1_status)? ' (statut : ' . $match->match_team1_status . ')':''),
			'team2'    => $match->team2_name . ( ! empty($match->match_team2_status)? ' (statut : ' . $match->match_team2_status . ')':''),
			'referees' => $referees,
		);
		$this->template->set_template('mail');

		$this->template->write_view('main_content', 'templates/' . $template, $data, true);
		$message = $this->template->render('', true);
		return self::_sendMail($recipients, $subject, $message);
	}


	/**
	 * send an alert when a referee cannot refer a match
	 *
	 * @param object $match
	 * @param query result $users
	 *
	 * @return bool
	 */
	function sendAlertRefereeNok($match, $users)
	{
		$subject = 'CRH Pays de la Loire : arbitre indisponible';
		$recipients = self::_formatRecipients($users);
		$referees = Misc_model::formatReferees($match);
		$data = array(
			'date'     => $match->match_date_format,
			'hour'     => $match->match_time_format,
			'place'    => $match->place_name,
			'cs'       => $match->cs_name,
			'cat'      => $match->category_name,
			'team1'    => $match->team1_name . ( ! empty($match->match_team1_status)? ' (statut : ' . $match->match_team1_status . ')':''),
			'team2'    => $match->team2_name . ( ! empty($match->match_team2_status)? ' (statut : ' . $match->match_team2_status . ')':''),
			'referees' => $referees,
		);
		$this->template->set_template('mail');

		$this->template->write_view('main_content', 'templates/mail_club_referee_manager_referee_nok_alert', $data, true);
		$message = $this->template->render('', true);
		return self::_sendMail($recipients, $subject, $message);
	}


	/**
	 * send a notification to summon referees
	 *
	 * @param object $match
	 * @param query result $users
	 *
	 * @return bool
	 */
	function sendRefereeNotification($match, $users)
	{
		$subject = 'CRH Pays de la Loire : convocation arbitrage';
		$recipients = self::_formatRecipients($users);
		$referees = Misc_model::formatReferees($match);
		$data = array(
			'date'     => $match->match_date_format,
			'hour'     => $match->match_time_format,
			'place'    => $match->place_name,
			'cs'       => $match->cs_name,
			'cat'      => $match->category_name,
			'team1'    => $match->team1_name . ( ! empty($match->match_team1_status)? ' (statut : ' . $match->match_team1_status . ')':''),
			'team2'    => $match->team2_name . ( ! empty($match->match_team2_status)? ' (statut : ' . $match->match_team2_status . ')':''),
			'referees' => $referees,
		);
		$this->template->set_template('mail');
		$this->template->write_view('main_content', 'templates/mail_referee_notification', $data, true);
		$message = $this->template->render('', true);
		return self::_sendMail($recipients, $subject, $message);
	}


	/**
	 * send a notification to ask a club to summon referees
	 *
	 * @param object $match
	 * @param query result $users
	 *
	 * @return bool
	 */
	function sendClubRefereeNotification($match, $users)
	{
		$subject = 'CRH Pays de la Loire : arbitrage de match';
		$recipients = self::_formatRecipients($users);
		$referees = Misc_model::formatReferees($match);
		$data = array(
			'date'     => $match->match_date_format,
			'hour'     => $match->match_time_format,
			'place'    => $match->place_name,
			'cs'       => $match->cs_name,
			'cat'      => $match->category_name,
			'team1'    => $match->team1_name . ( ! empty($match->match_team1_status)? ' (statut : ' . $match->match_team1_status . ')':''),
			'team2'    => $match->team2_name . ( ! empty($match->match_team2_status)? ' (statut : ' . $match->match_team2_status . ')':''),
			'referees' => $referees,
		);
		$this->template->set_template('mail');
		$this->template->write_view('main_content', 'templates/mail_club_referee_notification', $data, true);
		$message = $this->template->render('', true);
		return self::_sendMail($recipients, $subject, $message);
	}


	/**
	 * send an alert to club manager & team manager to ask to complete a match
	 *
	 * @param object $match
	 * @param array $users
	 *
	 * @return bool
	 */
	function sendAlertMeetingIncomplete($match, $users)
	{
		$subject = 'CRH Pays de la Loire : ALERTE convocation d équipe, votre action est requise';
		$recipients = self::_formatRecipients($users);
		$referees = Misc_model::formatReferees($match);
		$data = array(
			'date'     => $match->match_date_format,
			'hour'     => $match->match_time_format,
			'place'    => $match->place_name,
			'cs'       => $match->cs_name,
			'cat'      => $match->category_name,
			'team1'    => $match->team1_name . ( ! empty($match->match_team1_status)? ' (statut : ' . $match->match_team1_status . ')':''),
			'team2'    => $match->team2_name . ( ! empty($match->match_team2_status)? ' (statut : ' . $match->match_team2_status . ')':''),
			'referees' => $referees,
		);
		$this->template->set_template('mail');
		$this->template->write_view('main_content', 'templates/mail_club_manager_alert', $data, true);
		$message = $this->template->render('', true);
		return self::_sendMail($recipients, $subject, $message);
	}


	/**
	 * send an alert to referee manager & referee club manager to ask to summon referees for a match
	 *
	 * @param object $match
	 * @param array $users
	 *
	 * @return bool
	 */
	function sendAlertMeetingRefereeMissing($match, $users)
	{
		$subject = 'CRH Pays de la Loire : ALERTE convocation arbitre, votre action est requise';
		$recipients = self::_formatRecipients($users);
		$referees = Misc_model::formatReferees($match);
		$data = array(
			'date'     => $match->match_date_format,
			'hour'     => $match->match_time_format,
			'place'    => $match->place_name,
			'cs'       => $match->cs_name,
			'cat'      => $match->category_name,
			'team1'    => $match->team1_name . ( ! empty($match->match_team1_status)? ' (statut : ' . $match->match_team1_status . ')':''),
			'team2'    => $match->team2_name . ( ! empty($match->match_team2_status)? ' (statut : ' . $match->match_team2_status . ')':''),
			'referees' => $referees,
		);
		$this->template->set_template('mail');
		$this->template->write_view('main_content', 'templates/mail_club_referee_manager_alert', $data, true);
		$message = $this->template->render('', true);
		return self::_sendMail($recipients, $subject, $message);
	}


	/**
	 * send an alert to a team which has not confirmed its participation
	 *
	 * @param object $match
	 * @param array $users
	 *
	 * @return bool
	 */
	function sendAlertTeamNotConfirmed($match, $users)
	{
		$subject = 'CRH Pays de la Loire : ALERTE convocation d équipe, votre action est requise';
		$recipients = self::_formatRecipients($users);
		$referees = Misc_model::formatReferees($match);
		$data = array(
			'date'     => $match->match_date_format,
			'hour'     => $match->match_time_format,
			'place'    => $match->place_name,
			'cs'       => $match->cs_name,
			'cat'      => $match->category_name,
			'team1'    => $match->team1_name . ( ! empty($match->match_team1_status)? ' (statut : ' . $match->match_team1_status . ')':''),
			'team2'    => $match->team2_name . ( ! empty($match->match_team2_status)? ' (statut : ' . $match->match_team2_status . ')':''),
			'referees' => $referees,
		);
		$this->template->set_template('mail');
		$this->template->write_view('main_content', 'templates/mail_team_alert', $data, true);
		$message = $this->template->render('', true);
		return self::_sendMail($recipients, $subject, $message);
	}



	/**
	 * send an alert to a referee who has not confirmed his/her participation
	 *
	 * @param object $match
	 * @param array $users
	 *
	 * @return bool
	 */
	function sendAlertRefereeNotConfirmed($match, $users)
	{
		$subject = 'CRH Pays de la Loire : ALERTE convocation arbitre, votre action est requise';
		$recipients = self::_formatRecipients($users);
		$referees = Misc_model::formatReferees($match);
		$data = array(
			'date'     => $match->match_date_format,
			'hour'     => $match->match_time_format,
			'place'    => $match->place_name,
			'cs'       => $match->cs_name,
			'cat'      => $match->category_name,
			'team1'    => $match->team1_name . ( ! empty($match->match_team1_status)? ' (statut : ' . $match->match_team1_status . ')':''),
			'team2'    => $match->team2_name . ( ! empty($match->match_team2_status)? ' (statut : ' . $match->match_team2_status . ')':''),
			'referees' => $referees,
		);
		$this->template->set_template('mail');
		$this->template->write_view('main_content', 'templates/mail_referee_alert', $data, true);
		$message = $this->template->render('', true);
		return self::_sendMail($recipients, $subject, $message);
	}


	/**
	 * send an alert to a referee for an upcoming match
	 *
	 * @param object $match
	 * @param array $users
	 *
	 * @return bool
	 */
	function sendRefereeReminder($match, $users)
	{
		$subject = 'CRH Pays de la Loire : RAPPEL convocation arbitre';
		$recipients = self::_formatRecipients($users);
		$referees = Misc_model::formatReferees($match);
		$data = array(
			'date'     => $match->match_date_format,
			'hour'     => $match->match_time_format,
			'place'    => $match->place_name,
			'cs'       => $match->cs_name,
			'cat'      => $match->category_name,
			'team1'    => $match->team1_name . ( ! empty($match->match_team1_status)? ' (statut : ' . $match->match_team1_status . ')':''),
			'team2'    => $match->team2_name . ( ! empty($match->match_team2_status)? ' (statut : ' . $match->match_team2_status . ')':''),
			'referees' => $referees,
		);
		$this->template->set_template('mail');
		$this->template->write_view('main_content', 'templates/mail_referee_reminder', $data, true);
		$message = $this->template->render('', true);
		return self::_sendMail($recipients, $subject, $message);
	}


	/**
	 * send his login & password to a user
	 *
	 * @param query object $user
	 *
	 * @return bool
	 */
	function sendPassword($user)
	{
		$subject = 'CRH Pays de la Loire  : vos codes d accès';
		$recipients = self::_formatRecipients(array($user), 'to');
		$data = array(
			'login'    => $user->user_login,
			'password' => Misc_model::decryptStr($user->user_password),
		);
		$this->template->set_template('mail');
		$this->template->write_view('main_content', 'templates/mail_codes', $data, true);
		$message = $this->template->render('', true);
		return self::_sendMail($recipients, $subject, $message);
	}


	/**
	 * create an array of recipients
	 *
	 * @param array $users, an array of users
	 * @param string $type ; to|cc|bcc
	 *
	 * @return array $recipients
	 */
	function _formatRecipients($users, $type = 'bcc')
	{
		$recipients = array();
		foreach($users as $user) {
			if( ! empty($user->user_email)) {
				$recipients[] = array(
					'type' => $type,
					'name' => $user->user_name,
					'email' => $user->user_email
				);
			}
		}
		return $recipients;
	}


	/**
	 * send an email
	 *
	 * @param array $recipients
	 * @param string $subject
	 * @param string $message
	 *
	 * @return bool
	 */
	function _sendMail($recipients, $subject, $message)
	{
		$this->email->clear(true);
		$this->email->from(CRH_FROM_EMAIL, CRH_FROM_NAME);
		$array_to = array('contact@csport-roller.fr');
		$array_cc = array();
		$array_bcc = array();
		foreach($recipients as $recipient) {
			if($recipient['type'] == 'to') {
				$array_to[] = $recipient['email'];
			} elseif($recipient['type'] == 'cc') {
				$array_to[] = $recipient['email'];
			} elseif($recipient['type'] == 'bcc') {
				$array_bcc[] = $recipient['email'];
			}
		}
		if( ! empty($array_to)) {
			$this->email->to($array_to);
		}
		if( ! empty($array_cc)) {
			$this->email->cc($array_cc);
		}
		if( ! empty($array_bcc)) {
			$this->email->bcc($array_bcc);
		}
		//$this->email->to(array('m.kuntz@lezard-rouge.fr'));
		//$this->email->to(array('info@lezard-rouge.fr', 'vincent.badts@free.fr'));
		$this->email->subject($subject);
		$this->email->message($message);
		if( ! $this->email->send()) {
			$result = false;
		} else {
			$result = true;
		}
		$this->load->model('files_model');
		Files_model::writeLog($recipients);
		Files_model::writeLog($this->email->print_debugger());
		return $result;
	}

}

/* End of file emails_model.php */
/* Location: ./application/models/emails_model.php */
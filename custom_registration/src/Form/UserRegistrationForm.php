<?php

namespace Drupal\custom_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\user\Entity\User;

/**
 * Class PlaceOrderForm.
 */
class UserRegistrationForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'user_registration_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $user_ip = $_SERVER['REMOTE_ADDR'];
    
    // $client = \Drupal::httpClient();
     //$request = $client->get("http://www.geoplugin.net/php.gp?ip=$user_ip");
  //$response = $request->getBody();
 
    //$serializer = \Drupal::service('serializer'):
    //$entity = $serializer->serialize($response);
    //print_r($entity);die("123");
    $form['first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First Name'),
      '#weight' => '1',
      '#required' => TRUE,
    ];
    $form['last_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last Name'),
      '#weight' => '2',
      '#required' => TRUE,
    ];
    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#description' => $this->t('Please enter a valid email address'),
      '#weight' => '3',
      '#required' => TRUE,
    ];
    $form['phone_number'] = [
      '#type' => 'tel',
      '#title' => $this->t('Phone Number'),
      '#weight' => '4',
      '#required' => TRUE,
    ];
    $form['Company'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Company'),
      '#weight' => '5',
      '#required' => TRUE,
    ]; 
    $form['Address'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Address'),
      '#attributes' => array(
        'maxlength' => 255,
        'maxlength_js_label' => '@remaining characters left.',
      ),
      '#weight' => '6',
      '#required' => TRUE,
    ];
    $form['contact_messages'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Contact Message'),
      '#weight' => '7',
      '#required' => TRUE,
    ];
    $form['terms_conditions'] = array(
      '#title' => t(''),
      '#type' => 'checkboxes',
      '#description' => t(''),
      '#options' => ['Terms And Conditions'],
      '#weight' => '8',
      '#required' => TRUE,
    );  
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#weight' => '9',
    ]; 
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $user_ip = $_SERVER['REMOTE_ADDR'];
   
    $user = User::create(array(
      'name' => $form_state->getValue('email'),
      'field_last_name' => $form_state->getValue('last_name'),
      'field_first_name' => $form_state->getValue('first_name'),
      'field_phone_number' => $form_state->getValue('field_phone_number'),
      'field_company' => $form_state->getValue('company'),
      'field_address' => $form_state->getValue('address'),
      'field_message' => $form_state->getValue('contact_messages'),
      'field_user_ip' => $form_state->getValue($user_ip),
      'mail' => $form_state->getValue('email'),
      'status' => 1,
      ));
      $user -> save(); 

      $name = $form_state->getValue('first_name') . ' ' . $form_state->getValue('last_name');
      $params['message'] = $this->t("Dear Admin<br><br>
      The following user has been registered from the site, please see the details below:<br/>
      <br/>Name: @name<br/><br/>Email: @email<br/><br/><br/>
      <br/>Contact Details: @phone<br/><br/>Suggestion: @msg<br/><br/><br/>
      <br/>Address: @address<br/><br/>Company: @company<br/><br/><br/>",
        [
          '@name' => $name,
          '@email' => $form_state->getValue('email'),
          '@phone' => $form_state->getValue('phone_number'),
          '@msg' => $form_state->getValue('contact_messages'),
          '@address' => $form_state->getValue('address'),
          '@company' => $form_state->getValue('company'),
        ]
      );  

    
    $this->send_mail($params);    
    drupal_set_message(t('We will contact you shortly'), 'status');
    $this->get_text_file(strip_tags($params['message']), $name);    
  } 

   /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $first_name = $form_state->getValue('first_name');
    $last_name = $form_state->getValue('last_name');
    if (preg_match('/[^A-Za-z] /', $first_name)) {
      $form_state->setErrorByName('first_name', $this->t('The First Name should be alphabets.'));
    }
    if (preg_match('/[^A-Za-z] /', $last_name)) {
      $form_state->setErrorByName('last_name', $this->t('The Last Name should be alphabets.'));
    }
 } 

 /**
   * To get the text file.
   *
   * @param string $content
   *   The content inside the text file.
   * @param string $name
   *   The name of the user.
   */
  public function get_text_file($content, $name) {
    $file_name = $name . ".txt";
    $handle = fopen($file_name, "w");
    fwrite($handle, $content);
    fclose($handle);
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($file_name));
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_name));
    readfile($file_name);
    exit;
  }
  
   /**
   * Tosend a mail with user details.
   *
   * @param string $params
   *   Mail body.
   */
  public function send_mail($params) {
    $mailManager = \Drupal::service('plugin.manager.mail');
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $send = true;
    $to = \Drupal::config('system.site')->get('mail');
    $result = $mailManager->mail('custom_registration', 'user_registration', $to, $langcode, $params, NULL, $send); 
  }
}

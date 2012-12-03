# Yii Singly Extension

## NOTE
Still needs to be updated with better documentation

## Minimal Setup
* Download repo into the Yiiroot/protected/Extensions folder
* Add the following lines in Yiiroot/protected/config/main.php config file
		'components' => array(
			'singly' => array(
				'class' => 'ext.yii-singly.Singly',
				'CLIENT_ID' => YOUR_KEY_HERE,
				'CLIENT_SECRET' => YOUR_SECRET_HERE,
				'REDIRECT_URI' => YOUR_REDIRECT_URI_HERE,
			),
		),
* Now you can just do this to get login url
  Yii::app()->singly->getSinglyAuthenticationUrl('facebook')

* At your redirect uri 
  
  Yii::app()->singly->setAccessToken($_GET['code'])
  
  will log your user in and keep the **Session** stored  

* Get user's profile photo
		$name = Yii::app()->singly->fetchcustom('/profile');
		$user->name = $name['result']['thumbnail_url'];

* Post to User's facebook
		Yii::app()->singly->fetch('/types/statuses', array('to'=>'facebook', 'body'=>STATUS MESSAGE HERE), 'POST');
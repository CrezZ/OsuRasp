// 'use strict';
///////////////////////////////////////////////////////////////
/////  THis file used for WebPush notification processing and showing on client side
////   and install new Push notification


const PublicKey='BIqr_uQNyljwmAGj9W1YJd-yv84hAwLY2LFLeuBGPT6ZNpoKMBldEEwInZ_0Q5A-St6qY6ZowXTFfcWGISuRcfg';
const POST_URL="https://sip4.hvcloud.ru/t2/process_p.php?who=2&prep=100";


function urlB64ToUint8Array(base64String) {
  const padding = '='.repeat((4 - base64String.length % 4) % 4);
  const base64 = (base64String + padding)
    .replace(/\-/g, '+')
    .replace(/_/g, '/');

  const rawData = window.atob(base64);
  const outputArray = new Uint8Array(rawData.length);

  for (let i = 0; i < rawData.length; ++i) {
    outputArray[i] = rawData.charCodeAt(i);
  }
  return outputArray;
}



function requestPermission() {
  return new Promise(function(resolve, reject) {
    const permissionResult = Notification.requestPermission(function(result) {
      // Поддержка устаревшей версии с функцией обратного вызова.
      resolve(result);
    });

    if (permissionResult) {
      permissionResult.then(resolve, reject);
    }
  })
  .then(function(permissionResult) {
    if (permissionResult !== 'granted') {
      throw new Error('Permission not granted.');
    }
  });
}
function sendPOST(data){
	
var xhr = new XMLHttpRequest();
//var url = ;
xhr.open("POST", POST_URL, true);
xhr.setRequestHeader("Content-Type", "application/json");
xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
        // var json = JSON.parse(xhr.responseText);
        //console.log(json.email + ", " + json.password);
		console.log('Send POST Ok');
    }
};
//var data = JSON.stringify({"email": "hey@mail.com", "password": "101010"});
xhr.send(data);

}


function subscribeUserToPush() {
  return navigator.serviceWorker.register('js/service-worker.js')
  .then(function(registration) {
    var subscribeOptions = {
      userVisibleOnly: true,
      applicationServerKey: urlB64ToUint8Array(PublicKey)
      
    };

    return registration.pushManager.subscribe(subscribeOptions);
  })
  .then(function(pushSubscription) {
	  jj=pushSubscription.toJSON();
	  if(document.getElementById('who')!=null)  jj['who']=document.getElementById('who').value;
	  for(i=1;i<4;i++){
	    if(document.getElementById('s'+i)!=null) {
		  p="s"+i;
		  v=document.getElementById('s'+i).value;
		  jj[p]=v; 
		}
	  }
    console.log('PushSubscription: ', JSON.stringify(jj));
	sendPOST(JSON.stringify(jj));
    return pushSubscription;
  });
}


self.addEventListener('push', function(event) {
  console.log('[Service Worker] Push Received.');
  console.log(`[Service Worker] Push had this data: "${event.data.text()}"`);

  const title = 'OSU.RU';
  const options = {
    body: event.data.text(),
    icon: 'https://osu.ru/favicon.ico',
    //badge: 'images/badge.png'
  };

  event.waitUntil(self.registration.showNotification(title, options));
});


self.addEventListener('notificationclick', function(event) {
  console.log('[Service Worker] Notification click Received.');

  event.notification.close();

  event.waitUntil(
    clients.openWindow('https://developers.google.com/web/')
  );
});



<template>
    <div>
        <link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
        <form id="doLogin" class="form-group col-md-4 col-sm-8" autocomplete="off" @submit.prevent="login">
            <div class="logo"></div>
            <div class="login-block">
                <h1>Login</h1>
                <input type="text" placeholder="Email" name="usr_email" v-model="email" />
                <input type="password" placeholder="Password" name="usr_pwd" v-model="password" />
                <button type="submit" class="mui-btn mui-btn--raised custom-login-btn">SIGN IN</button>
            </div>
        </form>
    </div>
</template>
<script>
import VueAxios from 'axios';
export default {
    
   data() {
    	return {
      		email: '',
            password: '',
    	}
  	},
  	methods:{
        login()
        {
            let app = this;
            event.preventDefault();
            axios({
                 data: {
                     email: app.email,
                     password: app.password
                 },
                 method: 'post',
                 url: 'http://timesheet.amaxzatech.com/api/login',
                 crossDomain: true,
                 headers: {
                     'Content-Type': 'application/json',
                     'Accept': 'application/json',
                     'Access-Control-Allow-Origin': '*', 
                     'Access-Control-Allow-Methods': 'GET, POST',
                     'Access-Control-Allow-Headers': 'X-Requested-With',
                }
             })
             .then(function (data) {
               
             })
             .catch(function (response) {
                 
             });
        },
  	//  test(){
    //        console.log("here123");
    //     var self = this;
    //     VueAxios.get(this.tUrl)
	// 	  .then(function (response) {
	// 	    // handle success
	// 	    self.allRecord = response.data;
	// 	  })
	// 	  .catch(function (error) {
	// 	    // handle error
	// 	    console.log(error);
	// 	  });
  	// 	}
  	},
  	created() {
        console.log(axios.defaults.baseURL);
        console.log("here");
    	//this.test();
  	} 
}
</script>
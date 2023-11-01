<template>
    <body>
    <div>
      <nav id="nav">
        <ul>
          <router-link class="router-link" to="/">Home</router-link>
          <router-link class="router-link" to="/About">About</router-link>
          <router-link class="router-link" to="/MyUmzzal">MyUmzzal</router-link>
          <a class="router-link" href="http://cobluestar.ivyro.net/board/">게시판</a>
          <!--<li><a href="#">게시판</a></li>-->
       </ul>
      </nav>
  
      <header id="header">
        <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner">
            <div class="carousel-item active">
              <img src="./assets/images/catandme.png" class="d-block w-100" alt="cam">
            </div>
            <div class="carousel-item">
              <img src="./assets/images/catandme2.png" class="d-block w-100" alt="cam2">
            </div>
            <div class="carousel-item">
              <img src="./assets/images/catandme3.png" class="d-block w-100" alt="cam3">
            </div>
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
        </div>
      </header>
  
      <div id="container">
  
        <div id="content">
          <MyContent />
        </div>
  
      </div>
  
      <div id="clear"></div>
    </div>
  </body>
  </template>
  
  <script>
  import MyContent from './MyContent.vue';

  export default {
    components: {
      MyContent,
    },
  };
  </script>
  
  <style scoped>
  @media screen and (min-width: 768px) {
  body {
    box-sizing: border-box;
    width: 940px;
    margin: 0px auto;
  }
  /* body {
    width: 940px;
    margin: 0px auto;
  } */
  
  #nav {
    background: #000;
    padding: .15em 0;
    width: 940px;
    position: fixed;
    height: 50px;
    padding-top: 10px;
    top: 0px;
    z-index: 5;
  }
  
  #nav .router-link {
    margin-left: 2.5em;
    display: inline;
    list-style: none;
    text-decoration: none;
    color: white;
  }
  
  #nav .router-link:hover {
    font-weight: 800;
  }
  
  #nav li a {
    color: #fff;
    text-decoration: none;
  }

  #nav li a:hover {
    font-weight: 800;
  }
  
  #header {
    position: relative;
    color: #fff;
    font-size: 4em;
    z-index: 1;
    line-height: 180px;
    text-align: center;
    margin-bottom: 20px;
    margin-top: 40px;
  }

  .d-block w-100 {
    width: calc(var(--nav-height));
  }

  #content {
    float: left;
    width: 100%;
    padding: 20px;
    margin-bottom: 20px;
    border: 1px solid rgb(186, 186, 186);
    border-radius: 5px;
    position: relative; /*6.5 */
    z-index: 3;
  }
}

@media screen and (max-width: 767px) {

  body {
    margin: 0 auto;
    width: 100%;
  }

  #nav {
    width: 100%;
    background: #000;
    position: fixed;
    height: 50px;
    padding-top: 10px;
    top: 0px;
    z-index: 100;
  }

  #nav .router-link {
    text-align: center;
    margin-left: 2.2em;
    display: inline;
    list-style: none;
    text-decoration: none;
    font-size: small;
    color: white;
  }
  
  #nav .router-link:hover {
    font-weight: 800;
  }

  #header {
    position: relative;
    z-index: 1;
    margin-bottom: 20px;
    margin-top: 48px;
  }

  #content {
    float: left;
    width: 100%;
    padding: 20px;
    margin-bottom: 20px;
    margin: 0 auto;
    border: 1px solid rgb(186, 186, 186);
    border-radius: 5px;
    position: relative; /*6.5 */
    z-index: 2;
  }
}

  </style>
  
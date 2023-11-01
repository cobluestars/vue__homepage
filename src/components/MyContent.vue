<template>
  <div class="my-content">
    <!-- cardclass -->
    <div class="card" id="cardclass" style="width: 18rem;">
      <!-- cardclass 이미지 표시 -->
      <img :src="cardClassImage || require('@/assets/images/catandme.png')" class="card-img-top" alt="uploadimage" @click="uploadImage('cardclass')">
      <!-- cardclass 이미지 업로드 인풋 -->
      <input ref="cardClassImageInput" type="file" id="imageUpload" style="display: none" accept="image/*" @change="handleImageUpload($event, 'cardclass')">
      <div class="card-body">
        <div id="imageclick" class="badge text-bg-secondary">이미지를 클릭하면 이미지를 업로드합니다.</div>
        <!-- 제목 입력 필드 -->
        <div class="input-group mb-3">
          <span class="input-group-text" id="basic-addon1">제목</span>
          <input v-model="newTitle" type="text" id="Card title" class="form-control" aria-label="Username" aria-describedby="basic-addon1">
        </div> 
        <hr>
        <!-- 메시지 입력 필드 -->
        <div class="input-group">
          <textarea class="form-control" aria-label="With textarea" v-model="newMessage" rows="4" cols="50" placeholder="메시지를 입력하세요"></textarea>
        </div>
        <!-- 비밀번호 입력 필드 -->
        <div class="col-auto" id="pw">
          <label for="inputPassword" class="visually-hidden">password</label>
          <input v-model="addMessagePassword" type="password" class="form-control" id="inputPassword" placeholder="비밀번호">
        </div>
        <!-- 게시 버튼 -->
        <button type="button" id="submit" class="btn btn-success" @click="addMessage">게시</button>
      </div>
    </div>

    <hr>

    <!-- cardcontext -->
    <ul class="card-list">
      <div class="card" id="cardcontext" v-for="message in pagedCardcontexts" :key="message.id">
        <div class="image-container">
          <!-- 업로드된 이미지 표시 -->
          <img :src="message.uploadedImageUrl || cardClassImage" class="card-img-top" alt="uploaded-image" @click="message.editing ? uploadImage($event, message) : null">
          <!-- 이미지 업로드 인풋 -->
          <input type="file" class="image-upload" style="display: none" accept="image/*" @change="handleImageUpload($event, message)">
        </div>
        <div class="card-body">
          <span v-if="message.editing" id="imageclick2" class="badge text-bg-secondary">이미지 클릭 시 이미지를 업로드합니다.</span>
          <!-- 제목 -->
          <h5 v-if="!message.editing" class="card-title">{{ message.title }}</h5>
          <input v-else v-model="message.title" type="text" id="Card title" placeholder="제목을 수정하세요" class="form-control" aria-label="Username" aria-describedby="basic-addon1">
          <hr>
          <!-- 메시지 -->
          <div v-if="!message.editing" class="card-text" style="white-space: pre-line">{{ message.text }}</div> <!--줄바꿈 적용-->
          <!-- <p v-if="!message.editing" class="card-text">{{ message.text }}</p> -->
          <textarea v-else v-model="message.text" class="form-control" aria-label="With textarea" rows="4" cols="50" placeholder="메시지를 수정하세요"></textarea>
          <!-- 게시 일자 및 시간 -->
          <div class="card-footer text-muted">
            {{ formatDate(message.postedAt) }}
          </div>

          <div id="editdelete">
            <!-- 수정 시 비밀번호 입력 필드 -->
            <div v-if="message.editing" class="col-auto" id="pw1">
              <label for="inputPassword2" class="visually-hidden">Password</label>
              <input v-model="message.password" type="password" class="form-control" id="inputPassword1" placeholder="비밀번호" ref="passwordInput">
            </div>
            <div v-if="isDesktop">
              <span v-if="message.editing" class="badge text-bg-info" id="editingcancel">수정 취소: 틀린 비밀번호 입력 후 수정 버튼을 누르세요.</span>
            </div>
            <div class="info-badge" v-else>
              <div v-if="message.editing" class="badge text-bg-info" id="editingcancel">취소: 틀린 비밀번호 입력,<br>수정 버튼 클릭</div>
            </div>
            <!-- 삭제 시 비밀번호 입력 필드 -->
            <div v-if="message.deleting" class="col-auto" id="pw2">
              <label for="inputPassword2" class="visually-hidden">Password</label>
              <input v-model="message.password" type="password" class="form-control" id="inputPassword2" placeholder="비밀번호" ref="passwordInput">
            </div>
            <div v-if="isDesktop">
              <span v-if="message.deleting" class="badge text-bg-info" id="deletingcancel">삭제 취소: 틀린 비밀번호 입력 후 삭제 버튼을 누르세요.</span>
            </div>
            <div class="info-badge" v-else>
              <div v-if="message.deleting" class="badge text-bg-info" id="deletingcancel">취소: 틀린 비밀번호 입력,<br> 삭제 버튼 클릭</div>
            </div>

            <!-- 삭제 버튼 -->
            <a href="#" class="btn btn-dark" id="delete" v-if="!message.editing" @click="deleteMessage(message)">삭제</a>
            <!-- 수정 버튼 -->
            <a href="#" class="btn btn-secondary" id="modify" v-if="!message.deleting" @click="toggleEditing(message, $event)">수정</a>
          </div>
        </div>
      </div>
    </ul>

    <!--검색 기능 추가-->
    <!-- <div id="f5">
    <span class="badge rounded-pill text-bg-light">새로고침: "/새로고침" 입력 후 검색 클릭</span>
    </div> -->

    <div id="search" class="input-group mb-3">
      <button class="btn btn-outline-secondary" type="button" id="button-addon1" @click="Search">검색</button>
      <input v-model="keyword" type="text" class="form-control" placeholder="검색어 입력" aria-label="Example text with button addon" aria-describedby="button-addon1">
      <button class="btn btn-outline-secondary" type="button" id="button-addon1" @click="F5"> F5 </button>
    </div>
    <!-- 페이지 기능 추가 -->
    <nav aria-label="Page navigation example">
      <ul class="pagination justify-content-center">
        <li class="page-item" :class="{ disabled: currentPage === 1 }">
          <a class="page-link" @click="goToPage(currentPage - 1)">Previous</a>
        </li>
        <li class="page-item" v-for="page in totalPages" :key="page" :class="{ active: currentPage === page }">
          <a class="page-link" @click="goToPage(page)">{{ page }}</a>
        </li>
        <li class="page-item" :class="{ disabled: currentPage === totalPages }">
          <a class="page-link" @click="goToPage(currentPage + 1)">Next</a>
        </li>
      </ul>
    </nav>
    <!-- 페이지 기능 추가 -->
  </div>
</template>

<script>
import firebase from './Firebase.vue';

const db = firebase.firestore();

export default {
  name: 'MyContent',

  data() {
    return {
      newTitle: '',
      newMessage: '',
      cardcontexts: [],
      cardClassImage: '',
      defaultCardClassImage: require('@/assets/images/catandme.png'),
      currentPage: 1, // 페이지 기능 추가
      addMessagePassword: '', // 새 메시지 추가용 비밀번호를 저장하는 데이터 속성
      keyword: '', //검색 키워드
      // updatedCardcontexts: [], // keyword를 포함한 Cardcontexts
    };
  },

  computed: {
    pagedCardcontexts() {
      const cardcontextsPerPage = 10;
      const startIndex = (this.currentPage - 1) * cardcontextsPerPage;
      const endIndex = startIndex + cardcontextsPerPage;
      return this.cardcontexts.slice(startIndex, endIndex);
    },
    totalPages() {
      const cardcontextsPerPage = 10;
      return Math.ceil(this.cardcontexts.length / cardcontextsPerPage);
    },
  }, //페이지 기능 추가

  mounted() {
    if (window.innerWidth >= 768) {
        this.isDesktop = true;
      } else {
        this.isDesktop = false;
      }

    this.updateIsDesktop(); // 초기 실행
    window.addEventListener('resize', this.updateIsDesktop); // 변경 감지

    db.collection('MyData')
      .orderBy('postedAt', 'desc') // postedAt 필드를 내림차순으로 정렬
      .get()
      .then((querySnapshot) => {
        querySnapshot.forEach((doc) => {
          const data = doc.data();
          this.cardcontexts.push({
            id: doc.id,
            title: data.title,
            text: data.text,
            uploadedImage: null,
            uploadedImageUrl: data.uploadedImageUrl,
            editing: false,
            postedAt: data.postedAt.toDate(),
            correctPassword: data.correctPassword, // correctPassword 속성 추가
          });
        });
      })
      .catch((error) => {
        console.error('데이터를 가져오는 중 에러 발생: ', error);
      });
  },

  methods: {
    updateIsDesktop() {
    this.isDesktop = window.innerWidth >= 768;
    },

    formatDate(date) {
      const options = { year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: 'numeric' };
      return new Intl.DateTimeFormat('ko-KR', options).format(date);
    },

    addMessage() {
      if (this.addMessagePassword === '') {
        alert('비밀번호를 입력하세요.');
        return;
      }
      const message = {
        title: this.newTitle,
        text: this.newMessage,
        uploadedImage: null,
        uploadedImageUrl: this.cardClassImage || this.defaultCardClassImage,
        editing: false,
        postedAt: new Date(), //현재 시간
        password: this.addMessagePassword,
        correctPassword: this.addMessagePassword, // correctPassword 속성 추가
      };

      db.collection('MyData')
        .add(message)
        .then((docRef) => {
          message.id = docRef.id;
          this.cardcontexts.unshift(message); // 새로운 게시물을 배열의 맨 앞에 추가
          this.newTitle = '';
          this.newTitle = '';
          this.newMessage = '';
          this.cardClassImage = '';
          this.addMessagePassword = ''; // 비밀번호 입력 필드 초기화
        })
        .catch((error) => {
          console.error('메시지를 추가하는 중 에러 발생: ', error);
        });
    },

    uploadImage(target) {
      const inputElement = target === 'cardclass' ? this.$refs.cardClassImageInput : event.target.nextElementSibling;
      inputElement.click();
    },

    handleImageUpload(event, target) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = () => {
          if (target === 'cardclass') {
            this.cardClassImage = reader.result;
          } else {
            target.uploadedImage = reader.result;
            target.uploadedImageUrl = reader.result;
          }
        };
        reader.readAsDataURL(file);
      }
    },

    cancelDatabaseUpdate(message) {
      // 수정 취소 시 이전 데이터로 복원
      message.title = message.previousTitle;
      message.text = message.previousText;
      message.uploadedImageUrl = message.previousUploadedImageUrl;

      // Vue.js에서 데이터 업데이트를 감지하고 화면을 업데이트하도록 호출
      this.$forceUpdate();
    },

    toggleEditing(message, event) {
      const previousScrollTop = document.documentElement.scrollTop 
      || document.body.scrollTop || 0;

      // 이전 값 저장
      if (!message.previousTitle) {
        message.previousTitle = message.title;
      }
      if (!message.previousText) {
        message.previousText = message.text;
      }
      if (!message.previousUploadedImageUrl) {
        message.previousUploadedImageUrl = message.uploadedImageUrl;
      }
        
      // Vue.js에서 데이터 업데이트를 감지하고 화면을 업데이트하도록 호출
      this.$forceUpdate();

      // 비밀번호 확인 로직 추가
      const inputPassword = message.password; // 입력한 비밀번호
      const correctPassword = message.correctPassword; // 정확한 비밀번호

      if (!message.editing) {
        // 수정 모드로 전환
        message.editing = true;

        // 수정 모드로 전환되었을 때, 비밀번호 필드를 표시하기 위해 Vue.nextTick 사용
        this.$nextTick(() => {
          const passwordInput = event.target.closest('.card').querySelector('#inputPassword2');
          if (passwordInput) {
            setTimeout(() => {
              passwordInput.focus();
            }, 0);
          }
        });

        // 스크롤 위치 복원
        this.restoreScrollPosition(previousScrollTop);

      } else {
        if (inputPassword === correctPassword) {
          // 수정 완료
          message.editing = false;

          // 스크롤 위치 복원
          this.restoreScrollPosition(previousScrollTop);


          // 수정이 완료되었을 때 Firestore 데이터 업데이트
          db.collection('MyData')
            .doc(message.id)
            .update({
              title: message.title,
              text: message.text,
              uploadedImageUrl: message.uploadedImageUrl,
            })

            .then(() => {
              // 스크롤 위치 복원
              this.restoreScrollPosition(previousScrollTop);
              console.log('데이터가 성공적으로 업데이트되었습니다.');
            })


            .catch((error) => {
              // 수정 취소
              this.cancelDatabaseUpdate(message);

              // 화면 업데이트
              this.$nextTick(() => {
                  this.$forceUpdate();
              });

              // 스크롤 위치 복원
              this.restoreScrollPosition(previousScrollTop);
              console.error('데이터를 업데이트하는 중 에러 발생: ', error);
            });
        } else {
          // 스크롤 위치 복원
          this.restoreScrollPosition(previousScrollTop);

          alert('비밀번호가 일치하지 않습니다. 수정 모드를 종료합니다.');
          // 수정 모드 종료
          message.editing = false;
          // 입력된 비밀번호 초기화
          message.password = '';

          // 수정 취소
          this.cancelDatabaseUpdate(message);

          // 화면 업데이트
          this.$nextTick(() => {
              this.$forceUpdate();
          });

        }
      }
    },


    deleteMessage(message) {
      const previousScrollTop = document.documentElement.scrollTop 
      || document.body.scrollTop || 0;
    
      // 스크롤 위치 복원
      this.restoreScrollPosition(previousScrollTop);

      if (message.deleting) {
  
        const index = this.cardcontexts.indexOf(message);
        if (index > -1) {
          // 삭제 확인 작업을 위해 confirmDeleteMessage 메서드 호출
          this.confirmDeleteMessage(message, previousScrollTop)
            .then(() => {
              // 삭제 작업이 완료된 후 스크롤 위치 복원
              this.restoreScrollPosition(previousScrollTop);
            })
            .catch(() => {
              // 삭제 작업이 실패한 경우에도 스크롤 위치 복원
              this.restoreScrollPosition(previousScrollTop);
            });
        }
    
      } else {
        message.deleting = true; //삭제 모드 진입
      }
    },

    
    confirmDeleteMessage(message, previousScrollTop) {
      return new Promise((resolve, reject) => {
        const inputPassword = message.password; // 입력한 비밀번호
        const correctPassword = message.correctPassword; // 정확한 비밀번호

        if (inputPassword === correctPassword) {
          const index = this.cardcontexts.indexOf(message);
          if (index > -1) {
            this.cardcontexts.splice(index, 1);

            db.collection('MyData')
              .doc(message.id)
              .delete()
              .then(() => {
                console.log('데이터가 성공적으로 삭제되었습니다.');
                resolve();
              })
              .catch((error) => {
                console.error('데이터를 삭제하는 중 에러 발생: ', error);
                reject();
              })
              .finally(() => {
                // 삭제 작업이 완료된 후 스크롤 위치 복원
                this.restoreScrollPosition(previousScrollTop);
              });
          }
        } else {
          // 스크롤 위치 복원
          this.restoreScrollPosition(previousScrollTop);

          alert('비밀번호가 일치하지 않습니다. 삭제 모드를 종료합니다.');
          message.deleting = false; // 삭제 모드 종료
          message.password = ''; // 입력된 비밀번호 초기화
          reject();
        }
      });
    },

    // 스크롤 위치 복원
    restoreScrollPosition(previousScrollTop) {
      setTimeout(() => {
        window.scrollTo(0, previousScrollTop);
      }, 5);  //스크롤이 위로 올라갔다 내려오는 현상을(스크롤 복원) 0.005초 내에 구현
    },

    //새로고침
    F5() {
      location.reload();
      return;
    },


    //검색
    Search() {
      const searchKeyword = this.keyword.toLowerCase();
      
      // 키워드가 비어있다가 무언가 입력되었을 때, 검색 기능을 실행하지 않음
      if (searchKeyword === '') {
        return;
      }
      
      const filteredCardcontexts = this.cardcontexts.filter((message) => {
        const lowerCaseTitle = message.title.toLowerCase();
        const lowerCaseText = message.text.toLowerCase();
        return lowerCaseTitle.includes(searchKeyword) ||
        lowerCaseText.includes(searchKeyword);
      });

      if (filteredCardcontexts.length === 0) {
        alert('검색 결과가 없습니다.');
      }

      // 검색된 게시물로 카드 목록을 갱신.
      this.pagedCardcontexts.splice(0, this.pagedCardcontexts.length,
      ...filteredCardcontexts);

      // 검색 결과에 따라 페이지를 초기화.
      this.currentPage = 1;

      this.keyword = '';
    },


    // 페이지 이동
    goToPage(page) {
      this.currentPage = page;
      window.scrollTo(0, 0);
    },
  }
};
</script>
  
<style scoped>
  @media screen and (min-width: 768px) {

  .my-content {
    max-width: 900px;
    margin: 0 auto;
    color: white;
    padding: 20px;
    position: relative;
  }
  
  #cardclass {
    width: 600px;
    margin: 0 auto;
  }

  #imageclick {
    margin-left: 3px;
    margin-bottom: 20px;
  }

  #pw {
    float: left;
    width: 160px;
    margin-top: 4px;
  }

  #submit {
    float: right;
  }
  
  textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
  }
  
  button {
    padding: 10px 20px;
  }
  
  ul {
    list-style: none;
    padding: 0;
  }
  
  .card-list {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-start;
  }
  
  #cardcontext {
    width: 360px;
    margin: 20px;
  }

  #imageclick2 {
    margin-left: 52px;
    margin-bottom: 20px;
  }

  .card-text {
    margin-bottom: 14px;
  }

  #modify {
    position: relative;
    float: right;
    margin: 5px;
  }
  
  #delete {
    position: relative;
    float: right;
    margin: 5px;
  }

  #editingcancel {
    position: relative;
    margin: auto;
  }

  #deletingcancel {
    position: relative;
    margin: auto;
  }

  li {
    margin-bottom: 10px;
  }

  #search {
    width: 300px;
    margin: 0 auto;
  }

  #f5 {
    margin: 2px;
    text-align: center;
  }
}

@media screen and (max-width: 767px) {
  .my-content {
    max-width: 100%;
    margin: 0 auto;
    color: white;
    padding: 20px;
    position: relative;
  }
  
  #cardclass {
    width: 100%;
    margin: 0 auto;
  }

  #imageclick {
   margin-bottom: 20px;
   text-align: center;
  }

  #pw {
    float: left;
    width: 160px;
    margin-top: 4px;
  }

  #submit {
    float: right;
  }
  
  textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
  }
  
  button {
    padding: 10px 20px;
  }
  
  ul {
    list-style: none;
    padding: 0;
  }
  
  .card-list {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-start;
  }
  
  #cardcontext {
    width: 100%;
    margin: 20px;
  }

  #imageclick2 {
    margin-bottom: 20px;
  }

  .card-text {
    margin-bottom: 14px;
  }

  #inputpassword1 {
    text-align: center;
  }

  #inputpassword2 {
    text-align: center;
  }

  #modify {
    position: relative;
    float: right;
    margin: 3px;
  }
  
  #delete {
    position: relative;
    float: right;
    margin: 3px;
  }

  .info-badge {
    display: inline-block;
    position: relative;
    margin-top: 5px;
  }

  li {
    margin-bottom: 10px;
  }

  #f5 {
    margin: 2px;
    text-align: center;
  }
}

</style>
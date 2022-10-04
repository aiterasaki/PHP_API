let editId = null
let editFlag = false


function renderListTable() {
  fetch('http://127.0.0.1/request.php', { // 第1引数に送り先
    method: 'GET', // メソッド指定
    headers: { 'Content-Type': 'application/json' }, // jsonを指定
  })
    .then(response => response.json()) //返ってきたレスポンス（echo json_encode($result);）をjsonで受け取って次のthenへ渡す
    .then(res => {
      // console.log(res); // やりたい処理
      outputHTML(res) //HTMLに出力する
      editId = null
      editFlag = false
    })
    .catch(error => {
      console.log(error) // エラー表示
    })
  console.log(editId)
}

function outputHTML(res) {
  const listTable = document.getElementById("list-table")
  const tbodyDel = listTable.tBodies
  if (tbodyDel && tbodyDel.length > 0) {
    tbodyDel[0].remove()
  }

  const tbody = listTable.createTBody()
  tbody.insertRow()

  res.forEach((element) => {  // 配列の中のオブジェクトの数だけ処理を繰り返す
    const tr = tbody.insertRow()
    // console.log(element)
    tr.setAttribute('onclick', `clickList(${element.id}, "${element.name}", "${element.memo}" )`)
    Object.keys(element).forEach((key) => { // ID, name, memoの3回繰り返す
      const td = tr.insertCell()
      td.appendChild(document.createTextNode(element[key]))
    });
    const td = tr.insertCell()
    td.innerHTML =
      `<input type="button" value="削除" name="del" onclick="deleteClickBtn(${element.id})">`
  })
}

function outputClickBtn() {
  // PHPにGETする。(リクエスト送信)
  renderListTable()
};

function createClickBtn() {
  const json_data = { name: '', memo: '' };
  json_data.name = document.getElementById("name").value
  json_data.memo = document.getElementById("memo").value

  //入力値が’’の場合、処理を中断する。
  if ((json_data.name === '') || (json_data.memo === '')) return

  // PHPにPOSTする。(リクエスト送信)
  fetch('http://127.0.0.1/request.php', { // 第1引数に送り先
    method: 'POST', // メソッド指定
    headers: { 'Content-Type': 'application/json' }, // jsonを指定
    body: JSON.stringify(json_data) // json形式に変換して添付
  })
    .then(response => {
      console.log(response) // やりたい処理
      renderListTable()
    })
    .catch(error => {
      console.log(error) // エラー表示
    });
}

function deleteClickBtn($id) {
  const json_data = { list_id: $id };
  fetch('http://127.0.0.1/request.php', { // 第1引数に送り先
    method: 'DELETE', // メソッド指定
    headers: { 'Content-Type': 'application/json' }, // jsonを指定
    body: JSON.stringify(json_data) // json形式に変換して添付
  })
    .then(response => {
      console.log(response) // やりたい処理
      renderListTable()
    })
    .catch(error => {
      console.log(error) // エラー表示
    });
}

function updateClickBtn() {

  const json_data = { name: '', memo: '' };
  json_data.name = document.getElementById("name").value
  json_data.memo = document.getElementById("memo").value
  json_data.list_id = editId;

  console.log(json_data)

  //入力値が’’の場合、処理を中断する。
  if ((json_data.name === '') || (json_data.memo === '')) {
    alert('入力してください')
    return
  }

  if (!editFlag) {
    alert('リストが選択されてません。')
    return
  }

  fetch('http://127.0.0.1/request.php', { // 第1引数に送り先
    method: 'PUT', // メソッド指定
    headers: { 'Content-Type': 'application/json' }, // jsonを指定
    body: JSON.stringify(json_data) // json形式に変換して添付
  })
    .then(response => {
      console.log(response) // やりたい処理

      // 選択されてるID
      // console.log(editId)

      renderListTable()
    })
    .catch(error => {
      console.log(error) // エラー表示
    });
}


function clickList(id, name, memo) {
  editId = id
  editFlag = true
  const nameElement = document.getElementById("name")
  const memoElement = document.getElementById("memo")
  nameElement.value = name
  memoElement.value = memo
}

renderListTable()

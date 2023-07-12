window.addEventListener("load", () => {
  document.getElementById("add").addEventListener("click", () => {
    let insert_elements = document.querySelectorAll(
      "[data-list-insert-number]"
    );
    
    let number =
      Number(
        insert_elements[insert_elements.length - 1].dataset.listInsertNumber
      ) + 1;
    let html = `
        <p data-list-insert-number="${number}" data-list="true">
        <input type="hidden" name="insert_checkbox1[${number}]" value="0">
            <input type="hidden" name="insert_checkbox2[${number}]" value="0">
            <span>項目1</span><input type="checkbox" name="insert_checkbox1[${number}]" value="1">
            <span>項目2</span><input type="checkbox" name="insert_checkbox2[${number}]" value="1">
            <span>タイトル</span><input type="text" class="title" name="insert_title[${number}]">
            <button type="button" data-insert-delete="${number}">削除</button>
        </p>
        `;
    let update_elements = document.querySelectorAll(
      "[data-list-update-number]"
    );
    let all_elements = document.querySelectorAll("[data-list]");
    all_elements[all_elements.length - 1].insertAdjacentHTML("afterend", html);

    document
      .querySelector(`[ data-insert-delete='${number}']`)
      .addEventListener("click", () => {
        document
          .querySelector(`[data-list-insert-number="${number}"]`)
          .remove();
      });
  });
  Array.from(document.querySelectorAll("[data-update-delete]")).forEach((e) => {
    e.addEventListener("click", (e2) => {
      document.querySelector(
        "[data-update-delete-value='" + e2.target.dataset.updateDelete + "']"
      ).value = 1;
      e2.target.parentNode.style.display = "none";
    });
  });
  document.getElementById("regist").addEventListener("click", () => {
    let form = document.forms[0];
    form.method = "post";
    form.action = "sample.php";
    form.submit();
  });
});

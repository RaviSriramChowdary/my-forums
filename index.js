function fetchTopics() {
   $.get("fetchTopics.php", function (data) {
      data = JSON.parse(data);
      $("#topics").empty();
      $("#topics").html(data.html);
      console.log(data.array);

      likearray = ["like", "dislike"];
      for (let topicId in data.array) {
         $("#topic" + topicId + " > div.heading").click(function () {
               
            $(
               "#topic" + topicId + " > div.commentBox"
            ).slideToggle();
         });
         $('#topic' + topicId + ' > div.commentBox > div.addComment > form').submit(function (e) {
            e.preventDefault();
            $.post('createComment.php', { topicId: topicId, comment: $("#topic" + topicId + " > div.commentBox > div.addComment > form > textarea").val() }, function () {
               fetchTopics();
            })
         });
      }
      for (let topicId in data.array) {
            
         for (let commentId of data.array["" + topicId]) {
            $(
               "#comment" + commentId + " > div > div.options > span.reply"
            ).click(function () {
               console.log("hello");
               
            });
         }
      }
      for (let type of likearray) {
         for (let topicId in data.array) {
            $("#topic" + topicId + " > div.options > span." + type).click(
               function () {
                  $.post(
                     "likeChange.php",
                     {
                        lValue: type === "like" ? 1 : -1,
                        topicId: topicId,
                        lType: 0,
                     },
                     function () {
                        console.log("Changed like status for comment");
                        fetchTopics();
                     }
                  );
               }
            );
            for (let commentId of data.array["" + topicId]) {
               $(
                  "#comment" + commentId + " > div > div.options > span." + type
               ).click(function () {
                  $.post(
                     "likeChange.php",
                     {
                        lValue: type === "like" ? 1 : -1,
                        commentId: commentId,
                        lType: 1,
                     },
                     function () {
                        console.log("Changed like status for comment");
                        fetchTopics();
                     }
                  );
               });
            }
         }
      }
   });
}

$(document).ready(function () {
   console.log($(".message")[0]);
   fetchTopics();
   if ($("#showForm").val() == "1") {
      $("#newTopicBlock").show();
   }
   $("#addNewTopic").click(function () {
      $("#newTopicBlock").show();
   });
   $("#topicbackdrop").click(function () {
      $("#newTopicBlock").hide();
   });

   $("#create").click(function (e) {
      let form = document.getElementById("topicForm");
      e.preventDefault();

      var formdata = new FormData(form);

      $("#topicForm .highlight").each(function () {
         $(this).removeClass("highlight");
      });

      $.ajax({
         url: "createTopic.php",
         data: formdata,
         processData: false,
         type: "POST",
         contentType: false,
         success: function (data) {
            data = JSON.parse(data);
            if (data.error !== false) {
               $("#errorMsgForm").text(data.error);
            } else {
               $("#successMsgIndex").text("Succesfully added the topic !!");
            }
            if (data.highlight !== false) {
               $("#" + data.highlight).addClass("highlight");
               $("#" + data.highlight).on("input", function () {
                  $(this).removeClass("highlight");
               });
            }
            if (data.showForm === 0) {
               fetchTopics();
               $("#newTopicBlock").hide();
            }
         },
      });
   });

   $("#discard").click(function (e) {
      e.preventDefault();
      document.getElementById("topicForm").reset();
      $("#newTopicBlock").hide();
      $("#errorMsgForm").html("");
      $("#topicForm .highlight").each(function () {
         $(this).removeClass("highlight");
      });
   });
});

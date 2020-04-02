function AjaxInit(URL, Data, isRedirect = false, isSelfRedirect = false, showNotification = false) {
    $.ajax({
        url: URL,
        data: Data,
        type: 'POST',
        success: function (resp) {
          
            try {
                var Response = JSON.parse(resp);
                if (Response.StatusCode == "00") {
                    if (showNotification) {
                        succesMessage(Response.StatusMessage);
                        setTimeout(() => {
                            location.href = Response.RedirectUrl;
                        }, 2000);
                    } else {
                        if (isRedirect) {
                            location.href = Response.RedirectUrl;
                        } else {
                            succesMessage(Response.StatusMessage);
                        }
                        if (isSelfRedirect) {
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        }
                    }
                } else if (Response.StatusCode == "99") {
                    errorMessage(Response.StatusMessage);
                }
                else {
                    console.log(resp);
                    fatalMessage();
                }
            }
            catch (err) {
                console.log(err);
                fatalMessage();
            }



        },
        error: function (error) {
            console.log(error);
            fatalMessage();


        },
        cache: false,
        contentType: false,
        processData: false
    }); // ajax asynchronus request 
    //the following line wouldn't work, since the function returns immediately
    //return data; // return data from the ajax request
}
function SimpleAjaxInit(URL, Data, isRedirect = false, isSelfRedirect = false, showNotification = false) {
    $.post(URL, Data)
        .done(function (resp) {
            
            try {
                var Response = JSON.parse(resp);
                if (Response.StatusCode == "00") {
                    if (isRedirect) {
                        if (showNotification) {
                            succesMessage(Response.StatusMessage);
                            setTimeout(() => {
                                location.href = Response.RedirectUrl;
                            }, 2000);
                        } else {
                            location.href = Response.RedirectUrl;
                        }

                    } else {
                        succesMessage(Response.StatusMessage);
                    }
                    if (isSelfRedirect) {
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    }
                } else if (Response.StatusCode == "99") {
                    errorMessage(Response.StatusMessage);
                }
                else {
                    console.log(resp);
                    fatalMessage();
                }
            }
            catch (err) {
                console.log(err);
                fatalMessage();
            }
        })
        .fail(function (error) {
            console.log(error);
            fatalMessage();
        })

}
function succesMessage(message) {
    alertify.success(message);
}
function errorMessage(message) {
    alertify.error(message);
}
function fatalMessage() {
    alertify.error('Internal server error occurred');
}
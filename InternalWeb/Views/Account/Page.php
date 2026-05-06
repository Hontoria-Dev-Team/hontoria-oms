<!DOCTYPE html>
<html>

<head>
    <title>Account Panel - Hontoria OMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
    <link rel="stylesheet" href="../.CSS/StaffPage.css">
    <style>
        .asideLayout>main>section {
            min-width: unset;
        }

        :has(> a[href="index.php?page=logout"]) {
            padding: 0.5rem !important;
        }

        @media (max-height: 600px) {
            .asideLayout>main>section {
                justify-content: flex-start;
                overflow-x: scroll;
                padding: 0.3rem !important;
            }

            .asideLayout>main>section>:last-child {
                position: sticky !important;
                background: none;
                bottom: 0 !important;
                right: 0 !important;
                padding: 0 !important;
            }

            .asideLayout>main>section>:last-child>:nth-child(1) {
                width: 12rem !important;
            }

            .asideLayout>main>section>:last-child>.gradientBorderDiag {
                display: none;
            }
        }

        @media (max-width: 450px) {
            #userImageContainer img {
                height: unset !important;
                width: 75% !important;
            }

            .asideLayout>main>h1 {
                font-size: 1.25rem !important;
            }

            .asideLayout>main>h1>img {
                display: block !important;
            }

            .asideLayout>main>section {
                justify-content: flex-start;
                overflow-x: scroll;
                padding: 0.3rem !important;
            }

            .asideLayout>main>section>* {
                min-width: 85vw !important;
                max-width: 85vw !important;
            }

            .asideLayout>main>section>:nth-child(1) {
                flex-direction: column;
            }

            .asideLayout>main>section>:nth-child(1)>*,
            .asideLayout>main>section>:nth-child(1)>:nth-child(2)>* {
                width: 100% !important;
            }

            .asideLayout>main>section>:nth-child(1)>:nth-child(1) {
                min-height: 150px !important;
                width: unset !important;
                aspect-ratio: 1 / 1;
            }

            .asideLayout>main>section>:last-child {
                position: sticky !important;
                background: none;
                bottom: 0 !important;
                right: 0 !important;
                padding: 0 !important;
            }

            .asideLayout>main>section>:last-child>:nth-child(1) {
                height: 100% !important;
                width: 100% !important;
            }

            .asideLayout>main>section>:last-child>.gradientBorderDiag {
                display: none;
            }

            .triItemLayout {
                flex-direction: column;
            }

            .triItemLayout>* {
                width: 100%;
            }
        }
    </style>
</head>

<body class="asideLayout fixedScreen">
    <?php include("../Views/.Components/SideBar.php"); ?>
    <main class="columnLayout midGap">
        <h1 class="titleLogo minGap tinHeight">
            <img src="../../Shared/Img/AccountSettingsIcon.png" alt="AccountSettings"> Account Settings
        </h1>
        <?php include("../Views/.Components/MessageBox.php"); ?>
        <section class="centerColumnLayout midGap flexMax">
            <div class="rowLayout midGap extraWidth">
                <section class="centerColumnLayout box roundedMid centerHoriSelf flexMax clickable noFlexBasis noMinHeight">
                    <div class="centerColumnLayout fullDimensions" id="userImageContainer" data-image="../../Storage/AccountImages/<?= htmlspecialchars($accountImage) ?>">
                        <?php if (empty($accountImage)): ?>
                            <img src="../../Shared/Img/PersonIcon.png" alt="Person" class="midHeight">
                            <h3>Upload Account Photo</h3>
                        <?php else: ?>
                            <img src="../../Storage/AccountImages/<?= htmlspecialchars($accountImage) ?>" alt="Account Photo"
                                class="roundedMin shadowed imageCoverFull squareSize">
                        <?php endif; ?>
                    </div>
                    <div class="gradientBorderDiag"></div>
                </section>
                <div class="centerColumnLayout midGap flexMax">
                    <section class="centerColumnLayout box roundedMid centerHoriSelf">
                        <form method="POST" action="index.php?page=account&action=rename" class="centerColumnLayout minGap fullWidth">
                            <?php echo CsrfM::getTokenField(); ?>
                            <div class="fullWidth columnLayout">
                                <h3 class="leftStart">Change Username</h3>
                                <div class="rowLayout minGap">
                                    <input type="text" name="username" required="true" placeholder="<?php echo $_SESSION['username']; ?>" class="flexMax">
                                    <input type="submit" name="submit" value="Update" class="fullWidth importantInput flexMin shadowed noBorder">
                                </div>
                            </div>
                        </form>
                        <div class="gradientBorderDiag"></div>
                    </section>
                    <section class="centerColumnLayout box roundedMid centerHoriSelf">
                        <form method="POST" action="index.php?page=account&action=updateContacts" class="centerColumnLayout minGap fullWidth">
                            <?php echo CsrfM::getTokenField(); ?>
                            <div class="fullWidth">
                                <h3 class="leftStart">Change Contact Information</h3>
                                <div class="tinGap columnLayout">
                                    <div class="fullWidth">
                                        <label for="phoneNum" class="leftStart">Phone Number</label>
                                        <input type="tel" name="phoneNum" placeholder="<?php echo $_SESSION['phoneNumber']; ?>" pattern="^09\d{9}$" class="fullWidth"
                                            value="<?php echo htmlspecialchars($phoneNum ?? ''); ?>">
                                    </div>
                                    <div class="fullWidth">
                                        <label for="emailAddress" class="leftStart">Email Address</label>
                                        <input type="email" class="fullWidth" name="emailAddress" placeholder="<?php echo $_SESSION['email']; ?>">
                                    </div>
                                </div>
                            </div>
                            <input type="submit" name="submit" value="Update" class="fullWidth importantInput shadowed noBorder">
                        </form>
                        <div class="gradientBorderDiag"></div>
                    </section>
                </div>
            </div>
            <section class="centerColumnLayout box roundedMid extraWidth centerHoriSelf">
                <form method="POST" action="index.php?page=account&action=setUserNote" class="centerColumnLayout minGap fullWidth">
                    <?php echo CsrfM::getTokenField(); ?>
                    <div class="fullWidth">
                        <h3 class="leftStart">Set User Note</h3>
                        <div class="minGap rowLayout">
                            <input type="text" name="userNote" placeholder="Enter your note here..." class="flexMax capitalFirst" value="<?= $note ?>">
                            <input type="submit" name="submit" value="Submit" class="importantInput shadowed noBorder">
                        </div>
                    </div>
                </form>
                <div class="gradientBorderDiag"></div>
            </section>
            <section class="centerColumnLayout box roundedMid extraWidth centerHoriSelf">
                <form method="POST" action="index.php?page=account&action=changePassword" class="centerColumnLayout minGap fullWidth">
                    <?php echo CsrfM::getTokenField(); ?>
                    <div class="fullWidth">
                        <h3 class="leftStart">Change Password</h3>
                        <p class="marginMicro">Passwords must have at least 8 characters, and must contain a number, alphabet, and symbol.</p>
                        <div class="triItemLayout minGap rowLayout">
                            <div class="fullWidth columnLayout">
                                <label for="passwordCurrent" class="leftStart">Current Password</label>
                                <input type="password" name="passwordCurrent" required="true">
                            </div>
                            <div class="fullWidth columnLayout">
                                <label for="passwordNew" class="leftStart">New Password</label>
                                <input type="password" name="passwordNew" pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}$" required>
                            </div>
                            <div class="fullWidth columnLayout">
                                <label for="passwordRetype" class="leftStart">Retype Password</label>
                                <input type="password" name="passwordRetype" pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}$" required>
                            </div>
                        </div>
                    </div>
                    <input type="submit" name="submit" value="Change Password" class="fullWidth importantInput shadowed noBorder">
                </form>
                <div class="gradientBorderDiag"></div>
            </section>
            <section class="centerColumnLayout box roundedMid souEastAbsolute edgeCorner">
                <a href="index.php?page=logout" class="fullWidth criticalInput roundedMin minPadding midWidth centerColumnLayout outlineText shadowed">Logout</a>
                <div class="gradientBorderDiag"></div>
            </section>
        </section>
    </main>
    <?php include("../Views/.Components/ConfirmationBox.php"); ?>
    <?php include("../Views/.Components/ImageBox.php"); ?>
</body>
<script src="../.JS/ConfirmationBox.js"></script>
<script src="../.JS/CsrfHandler.js"></script>
<script src="../.JS/ImageBox.js"></script>
<script>
    const userImageContainer = document.getElementById('userImageContainer');

    document.addEventListener("DOMContentLoaded", () => {
        confirmationCancel.value = "No Cancel";
    });

    // Design Box logic function (User Image Upload)
    userImageContainer.addEventListener('click', function() {
        // Reset and prepare confirmation dialog
        confirmationContent.classList.add("fitWidth");
        confirmationForm.action = "index.php?page=account&action=uploadImage";

        // Create file input row
        const inputRow = document.createElement("div");
        inputRow.className = "tempElement centerHoriRowLayout minGap";
        confirmationForm.appendChild(inputRow);

        const label = document.createElement("b");
        label.textContent = "Upload File:";
        inputRow.appendChild(label);

        const fileInput = document.createElement("input");
        fileInput.type = "file";
        fileInput.name = "image";
        fileInput.accept = "image/*";
        fileInput.required = true;
        fileInput.className = "flexMax";
        inputRow.appendChild(fileInput);

        // Preview container (hidden initially if there is no image available)
        const previewContainer = document.createElement("div");
        previewContainer.className = "tempElement hidden centerHoriRowLayout minGap regPadding fitWidth scrollableX halfScreenMaxWidth fullMinWidth halfScreenHeight";
        confirmationForm.appendChild(previewContainer);

        // Preview image
        const uploadedImage = document.createElement("img");
        uploadedImage.className = "fullHeight roundedMin shadowed centerMarginsSelf";
        previewContainer.appendChild(uploadedImage);

        // Show image if account image is set
        if (userImageContainer.dataset.image != "../../Storage/AccountImages/") {
            uploadedImage.src = userImageContainer.dataset.image;
            previewContainer.classList.remove("hidden");
        }

        // Set dialog texts and button style
        confirmationTitle.innerHTML = "Upload Your Image";
        confirmationText.innerHTML = "Please upload a photo to represent you.";
        confirmationSubmit.value = "Upload";
        confirmationSubmit.classList.add("yellowBG", "whiteText", "noBorder");

        // Enable multipart form data
        confirmationForm.enctype = "multipart/form-data";
        confirmation.style.display = 'flex';

        // Handle file selection
        fileInput.addEventListener('change', () => {
            previewContainer.innerHTML = '';
            const files = fileInput.files;

            if (files.length === 0) {
                previewContainer.classList.add("hidden");
                return;
            }

            // Single file only (since multiple is not allowed)
            if (files.length > 1) {
                alert("Only one file allowed");
                fileInput.value = "";
                previewContainer.classList.add("hidden");
                return;
            }

            const design = files[0];
            if (!design.type.startsWith("image/")) {
                alert("Only images are allowed");
                fileInput.value = "";
                previewContainer.classList.add("hidden");
                return;
            }

            // Show preview image
            uploadedImage.src = URL.createObjectURL(design);
            previewContainer.appendChild(uploadedImage);
            previewContainer.classList.remove("hidden");
        });
    });

    // Added cancellation events
    confirmationCancel.addEventListener('click', function() {
        document.querySelectorAll('.tempElement').forEach(function(elem) {
            elem.remove();
        });

        confirmationForm.removeAttribute("enctype");
        confirmationSubmit.classList.remove("hidden");
    });

    confirmationBG.addEventListener('click', function() {
        document.querySelectorAll('.tempElement').forEach(function(elem) {
            elem.remove();
        });

        confirmationForm.removeAttribute("enctype");
        confirmationSubmit.classList.remove("hidden");
    });
</script>

</html>
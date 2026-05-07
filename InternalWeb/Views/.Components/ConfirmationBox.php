<!DOCTYPE html>
<!-- Confirmation dialog box used across the application.
     Contains a POST form with a built-in CSRF token field via CsrfM::getTokenField(). -->
<section id="confirmationBox" class="centerColumnLayout topZ" style="display: none;">
    <div id="confirmationContent" class="centerColumnLayout roundedMid maxWidth">
        <div class="box centerColumnLayout roundedMid fullWidth fullHeight minGap">
            <h2 id="confirmationTitle">Confirmation Title</h2>
            <h4 id="confirmationText">Confirmation Text</h4>
            <form id="confirmationForm" method="POST" class="reverseColumnLayout fullWidth minGap">
                <?php echo CsrfM::getTokenField(); ?> <!-- CSRF protection included -->
                <div id="confirmationButtons" class="rowLayout minGap">
                    <!-- REVIEW: IDs "submitConfimationButton" and "cancelConfimationButton" may be typos. -->
                    <input type="submit" class="criticalInput flexMax shadowed noBorder" id="submitConfimationButton" value="Yes">
                    <input type="button" class="importantInput flexMax shadowed noBorder" id="cancelConfimationButton" value="No">
                </div>
            </form>
        </div>
        <div class="gradientBorderDiag"></div>
    </div>
    <div id="confirmationBackground"></div>
</section>
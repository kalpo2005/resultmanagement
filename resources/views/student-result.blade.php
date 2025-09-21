<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Result Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" xintegrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <style>
        /* Core Styles & Variables */
        :root {
            --bg-dark: #0D1117;
            --primary-dark: #161b22;
            --secondary-dark: #21262d;
            --border-dark: #30363d;
            --text-light: #F0F6FC;
            --text-muted: #8B949E;
            --accent-color: #58A6FF;
            --accent-hover: #80B9F8;
            --success-color: #3FB950;
            --error-color: #F85149;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-light);
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1rem;
        }

        main {
            width: 100%;
            max-width: 56rem;
            margin: auto;
        }

        /* Form Container */
        .form-container {
            background-color: var(--primary-dark);
            padding: 2rem;
            border-radius: 1.5rem;
            border: 1px solid var(--border-dark);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2), 0 4px 6px -2px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .form-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .form-header h1 {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .form-header p {
            color: var(--text-muted);
            margin-top: 0.5rem;
            font-size: 0.875rem;
        }

        .form-grid {
            display: grid;
            gap: 1rem;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label,
        .radio-group-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--text-muted);
            margin-bottom: 0.25rem;
        }

        .form-input,
        .form-select {
            background-color: var(--secondary-dark);
            color: var(--text-light);
            width: 100%;
            padding: 0.6rem;
            border: 1px solid var(--border-dark);
            border-radius: 0.375rem;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 2px var(--accent-color);
        }

        .radio-group {
            display: flex;
            gap: 0.75rem;
            background-color: var(--secondary-dark);
            border: 1px solid var(--border-dark);
            border-radius: 0.375rem;
            padding: 0.5rem;
        }

        .radio-group label {
            flex-grow: 1;
            text-align: center;
            padding: 0.25rem 0;
            font-size: 0.875rem;
            border-radius: 0.25rem;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }

        .radio-group input {
            display: none;
        }

        .radio-group input:checked+label {
            background-color: var(--accent-color);
            color: #0d1117;
        }

        .btn {
            background-color: var(--accent-color);
            color: #0d1117;
            font-weight: bold;
            padding: 0.6rem 1rem;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: var(--accent-hover);
        }

        .btn:disabled {
            cursor: not-allowed;
            opacity: 0.75;
        }

        .loader {
            margin-left: 0.5rem;
        }

        .hidden {
            display: none !important;
        }

        /* Result Overlay & Card */
        .result-overlay {
            position: fixed;
            inset: 0;
            background-color: rgba(17, 24, 39, 0.8);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            z-index: 40;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease-in-out;
        }

        .result-section {
            width: 100%;
            max-width: 64rem;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
        }

        .result-section::-webkit-scrollbar {
            width: 8px;
        }

        .result-section::-webkit-scrollbar-track {
            background: var(--primary-dark);
            border-radius: 10px;
        }

        .result-section::-webkit-scrollbar-thumb {
            background: var(--secondary-dark);
            border-radius: 10px;
        }

        .result-section::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .result-card {
            background-color: var(--primary-dark);
            color: var(--text-light);
            padding: 2.5rem;
            border-radius: 1.5rem;
            border: 1px solid var(--border-dark);
        }

        .card-header {
            position: relative;
            text-align: center;
            margin-bottom: 1rem;
        }

        .card-header h2 {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 0;
        }

        .card-header p {
            color: var(--text-muted);
            margin: 0.25rem 0 0;
        }

        .action-buttons {
            position: absolute;
            top: 50%;
            right: 0;
            transform: translateY(-50%);
            display: flex;
            gap: 0.75rem;
        }

        .btn-icon {
            background-color: var(--secondary-dark);
            color: var(--text-muted);
            border: 1px solid var(--border-dark);
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 9999px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .btn-icon:hover {
            background-color: #4B5563;
            color: var(--text-light);
        }

        .result-card hr {
            border: 0;
            height: 1px;
            background-color: var(--border-dark);
            margin: 1.5rem 0;
        }

        .student-info-block {
            display: flex;
            align-items: center;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .student-photo {
            flex-shrink: 0;
        }

        .student-photo img {
            width: 7rem;
            height: 7rem;
            border-radius: 9999px;
            object-fit: cover;
            background-color: var(--secondary-dark);
            border: 2px solid var(--border-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: bold;
        }

        .student-details-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.75rem 2.5rem;
            font-size: 0.875rem;
            flex-grow: 1;
        }

        .student-details-grid div strong {
            color: var(--text-muted);
            display: block;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .student-details-grid div span {
            font-weight: 600;
            font-size: 1rem;
        }

        .table-container {
            overflow-x: auto;
        }

        .marks-table {
            width: 100%;
            text-align: left;
            font-size: 0.875rem;
            border-collapse: collapse;
        }

        .marks-table th,
        .marks-table td {
            padding: 1rem 0.75rem;
            border-bottom: 1px solid var(--border-dark);
        }

        .marks-table thead th {
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom-width: 2px;
            white-space: nowrap;
        }

        .marks-table thead tr:first-child th {
            border-bottom: 1px solid var(--border-dark);
        }

        .marks-table tbody tr:last-child td {
            border-bottom: none;
        }

        .marks-table tfoot {
            font-weight: bold;
        }

        .marks-table tfoot td {
            border-top: 2px solid var(--border-dark);
            color: var(--text-light);
        }

        .marks-table .text-center {
            text-align: center;
        }

        .marks-table .text-right {
            text-align: right;
        }

        .marks-fail {
            color: var(--error-color) !important;
            font-weight: bold;
        }

        .result-summary {
            display: grid;
            gap: 1.5rem;
            margin-top: 2rem;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        }

        .summary-box {
            padding: 1.5rem;
            border-radius: 1rem;
            text-align: center;
        }

        .summary-box p:first-child {
            font-size: 0.875rem;
            font-weight: 600;
            margin: 0 0 0.5rem;
            text-transform: uppercase;
        }

        .summary-box p:last-child {
            font-size: 2rem;
            font-weight: bold;
            margin: 0;
            line-height: 1;
        }

        .summary-sgpa {
            background: linear-gradient(145deg, #4338CA, #4f46e5);
            border: 1px solid rgba(99, 102, 241, 0.5);
        }

        .summary-sgpa p:first-child {
            color: #A5B4FC;
        }

        .summary-result.pass {
            background: linear-gradient(145deg, #059669, #10b981);
            border: 1px solid rgba(34, 197, 94, 0.5);
        }

        .summary-result.pass p:first-child {
            color: #86EFAC;
        }

        .summary-result.fail {
            background: linear-gradient(145deg, #DC2626, #ef4444);
            border: 1px solid rgba(239, 68, 68, 0.5);
        }

        .summary-result.fail p:first-child {
            color: #FCA5A5;
        }

        .summary-percentage {
            background: linear-gradient(145deg, #2563EB, #3b82f6);
            border: 1px solid rgba(96, 165, 250, 0.5);
        }

        .summary-percentage p:first-child {
            color: #93C5FD;
        }

        .summary-rank {
            background: linear-gradient(145deg, #d946ef, #c026d3);
            border: 1px solid rgba(217, 70, 239, 0.5);
        }

        .summary-rank p:first-child {
            color: #f5d0fe;
        }


        .dialog-box {
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            z-index: 50;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease-in-out;
        }

        .dialog-content {
            background-color: var(--primary-dark);
            border-radius: 0.75rem;
            padding: 1.5rem;
            width: 100%;
            max-width: 24rem;
            text-align: center;
            transform: scale(0.95);
            transition: transform 0.3s ease-in-out;
        }

        .dialog-icon {
            width: 4rem;
            height: 4rem;
            border-radius: 9999px;
            margin: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.25rem;
            margin-bottom: 1rem;
        }

        .dialog-icon.success {
            background-color: rgba(22, 163, 74, 0.2);
            color: #4ADE80;
        }

        .dialog-icon.error {
            background-color: rgba(220, 38, 38, 0.2);
            color: #F87171;
        }

        .dialog-content h3 {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .dialog-content p {
            color: var(--text-muted);
        }

        .dialog-close-btn {
            margin-top: 1.5rem;
            width: 100%;
            background-color: var(--secondary-dark);
        }

        .dialog-close-btn:hover {
            background-color: var(--border-dark);
        }

        @media (min-width: 768px) {
            .form-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .form-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (max-width: 768px) {
            .student-info-block {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .student-details-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .result-summary {
                grid-template-columns: 1fr;
            }
        }

        /* PDF Export Specific Styles */
        body.pdf-export-mode {
            background-color: white !important;
        }

        body.pdf-export-mode .result-overlay {
            overflow: visible;
            background: none;
            backdrop-filter: none;
            display: block;
            position: static;
            padding: 0;
            z-index: 100;
        }

        body.pdf-export-mode .result-section {
            max-height: none;
            overflow: visible;
        }

        .pdf-export-mode .result-card {
            width: 1024px;
            box-sizing: border-box;
            margin: 0;
            padding: 1.5rem;
            border-radius: 0.5rem;
            font-size: 10pt;
            box-shadow: none !important;
        }

        .pdf-export-mode .card-header h2 {
            font-size: 16pt;
        }

        .pdf-export-mode .card-header p {
            font-size: 11pt;
        }

        .pdf-export-mode .student-info-block {
            margin-bottom: 1.5rem;
            gap: 1.5rem;
        }

        .pdf-export-mode .student-photo img {
            width: 6rem;
            height: 6rem;
        }

        .pdf-export-mode .student-details-grid {
            font-size: 10pt;
            gap: 0.5rem 2rem;
        }

        .pdf-export-mode .student-details-grid div span {
            font-size: 11pt;
        }

        .pdf-export-mode .marks-table {
            font-size: 8.5pt;
        }

        .pdf-export-mode .marks-table th,
        .pdf-export-mode .marks-table td {
            padding: 0.4rem 0.5rem;
        }

        .pdf-export-mode .result-summary {
            margin-top: 1.5rem;
            gap: 1rem;
        }

        .pdf-export-mode .summary-box {
            padding: 1.2rem;
        }

        .pdf-export-mode .summary-box p:first-child {
            font-size: 9pt;
        }

        .pdf-export-mode .summary-box p:last-child {
            font-size: 16pt;
        }

        .pdf-export-mode .action-buttons {
            display: none !important;
        }
    </style>
</head>

<body>
    <main>
        <!-- Form Section -->
        <div class="form-container">
            <div class="form-header">
                <h1>Student Result Portal</h1>
                <p>Enter your details to view your exam result.</p>
            </div>
            <form id="result-form">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="examType">Exam Type</label>
                        <select id="examType" name="examTypeId" class="form-select">
                            <option value="">Select Exam</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="seatNumber">Seat Number</label>
                        <input type="text" id="seatNumber" name="seatNumber" class="form-input" placeholder="e.g., 26840604">
                    </div>
                    <div class="form-group">
                        <label for="semester">Semester</label>
                        <select id="semester" name="semesterId" class="form-select">
                            <option value="">Select Semester</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" id="submitBtn" class="btn">
                            <span id="btn-text">Get Result</span>
                            <i id="loader" class="fas fa-spinner fa-spin loader hidden"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group" style="margin-top: 1rem;">
                    <label class="radio-group-label">Class</label>
                    <div class="radio-group">
                        <input type="radio" id="classA" name="studentClass" value="A" checked><label for="classA">A</label>
                        <input type="radio" id="classB" name="studentClass" value="B"><label for="classB">B</label>
                        <input type="radio" id="classC" name="studentClass" value="C"><label for="classC">C</label>
                        <input type="radio" id="classD" name="studentClass" value="D"><label for="classD">D</label>
                        <input type="radio" id="classE" name="studentClass" value="E"><label for="classE">E</label>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <!-- Result Overlay -->
    <div id="result-overlay" class="result-overlay">
        <div id="result-section" class="result-section">
            <div class="result-card printable-area">
                <div class="card-header">
                    <div class="header-text">
                        <h2 id="collegeName"></h2>
                        <p>Statement of Marks</p>
                        <p id="examName"></p>
                    </div>
                    <div class="action-buttons">
                        <button id="downloadBtn" class="btn-icon" title="Download PDF">
                            <i class="fas fa-download"></i>
                        </button>
                        <button id="closeResultBtn" class="btn-icon" title="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <hr>
                <div class="student-info-block">
                    <div class="student-photo">
                        <img id="profileImage" src="https://placehold.co/150x150/374151/9CA3AF?text=?" alt="Student Profile Image">
                    </div>
                    <div class="student-details-grid">
                        <div><strong>Student Name:</strong> <span id="studentName"></span></div>
                        <div><strong>Seat No:</strong> <span id="seatNo"></span></div>
                        <div><strong>Semester:</strong> <span id="semesterName"></span></div>
                        <div><strong>Enrollment No:</strong> <span id="EnrollmentNO"></span></div>
                    </div>
                </div>
                <div class="table-container">
                    <table class="marks-table">
                        <thead id="marks-table-head">
                            <!-- Header will be dynamically inserted here -->
                        </thead>
                        <tbody id="marks-body"></tbody>
                        <tfoot id="marks-table-foot">
                            <!-- Footer will be dynamically inserted here -->
                        </tfoot>
                    </table>
                </div>
                <div id="result-summary-container" class="result-summary">
                    <div id="sgpaContainer" class="summary-box summary-sgpa">
                        <p>SGPA</p>
                        <p id="sgpa"></p>
                    </div>
                    <div id="resultStatusContainer" class="summary-box summary-result">
                        <p>Result</p>
                        <p id="resultStatus"></p>
                    </div>
                    <div class="summary-box summary-percentage">
                        <p>Percentage</p>
                        <p id="percentage"></p>
                    </div>
                    <div id="rankContainer" class="summary-box summary-rank hidden">
                        <p id="rankTitle"></p>
                        <p id="rank"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dialog Box for errors -->
    <div id="dialog-box" class="dialog-box">
        <div id="dialog-content" class="dialog-content">
            <div id="dialog-icon" class="dialog-icon"></div>
            <h3 id="dialog-title"></h3>
            <p id="dialog-message"></p>
            <button id="dialog-close" class="btn dialog-close-btn">Close</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('result-form');
            const submitBtn = document.getElementById('submitBtn');
            const loader = document.getElementById('loader');
            const btnText = document.getElementById('btn-text');
            const resultOverlay = document.getElementById('result-overlay');
            const closeResultBtn = document.getElementById('closeResultBtn');
            const downloadBtn = document.getElementById('downloadBtn');
            const examTypeSelect = document.getElementById('examType');
            const semesterSelect = document.getElementById('semester');

            const dialogBox = document.getElementById('dialog-box');
            const dialogContent = document.getElementById('dialog-content');
            const dialogIcon = document.getElementById('dialog-icon');
            const dialogTitle = document.getElementById('dialog-title');
            const dialogMessage = document.getElementById('dialog-message');
            const dialogClose = document.getElementById('dialog-close');

            const API_BASE_URL = "{{ config('constant.api_base_url') }}";
            const jwtToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYXBpL2xvZ2luYWRtaW4iLCJpYXQiOjE3NTgxOTc0MjksImV4cCI6MTc1ODIwMTAyOSwibmJmIjoxNzU4MTk0MjksImqdGkiOiJPSEVOMTNGZlh6MDBPMW1hIiwic3ViIjoiMSIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.7FeQRJX9Jgl1zyONuudmwzma10ci68Xh3mmF0HFXoVs";

            const showLoader = (show) => {
                loader.classList.toggle('hidden', !show);
                btnText.textContent = show ? 'Fetching...' : 'Get Result';
                submitBtn.disabled = show;
            };

            const showDialog = (title, message, type = 'error') => {
                dialogTitle.textContent = title;
                dialogMessage.textContent = message;
                dialogIcon.className = `dialog-icon ${type}`;
                dialogIcon.innerHTML = type === 'success' ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>';
                dialogBox.style.opacity = '1';
                dialogBox.style.pointerEvents = 'auto';
                dialogContent.style.transform = 'scale(1)';
            };

            const hideDialog = () => {
                dialogBox.style.opacity = '0';
                dialogBox.style.pointerEvents = 'none';
                dialogContent.style.transform = 'scale(0.95)';
            };

            const showResultOverlay = () => {
                resultOverlay.style.opacity = '1';
                resultOverlay.style.pointerEvents = 'auto';
            };

            const hideResultOverlay = () => {
                resultOverlay.style.opacity = '0';
                resultOverlay.style.pointerEvents = 'none';
            };

            const populateDropdowns = async () => {
                try {
                    const examResponse = await fetch(`${API_BASE_URL}/examtype/dropdown`, {
                        method: 'GET',
                        headers: {
                            'Authorization': `Bearer ${jwtToken}`
                        }
                    });
                    if (!examResponse.ok) throw new Error('Failed to fetch exam types');
                    const examData = await examResponse.json();
                    if (examData.status && examData.data) {
                        examData.data.forEach(exam => {
                            examTypeSelect.add(new Option(exam.examName, exam.examTypeId));
                        });
                    }

                    const semesterResponse = await fetch(`${API_BASE_URL}/semester/dropdown`, {
                        method: 'GET',
                        headers: {
                            'Authorization': `Bearer ${jwtToken}`
                        }
                    });
                    if (!semesterResponse.ok) throw new Error('Failed to fetch semesters');
                    const semesterData = await semesterResponse.json();
                    if (semesterData.status && semesterData.data) {
                        semesterData.data.forEach(sem => {
                            semesterSelect.add(new Option(sem.semesterName, sem.semesterId));
                        });
                    }
                } catch (error) {
                    console.error("Dropdown fetch error:", error);
                    showDialog('Setup Error', 'Could not load required form data.');
                }
            };

            populateDropdowns();

            dialogClose.addEventListener('click', hideDialog);
            closeResultBtn.addEventListener('click', hideResultOverlay);
            resultOverlay.addEventListener('click', (e) => {
                if (e.target === resultOverlay) hideResultOverlay();
            });

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                hideResultOverlay();
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());

                if (!data.examTypeId || !data.seatNumber || !data.semesterId || !data.studentClass) {
                    showDialog('Input Error', 'Please fill out all the required fields.');
                    return;
                }

                showLoader(true);

                try {
                    const response = await fetch(`${API_BASE_URL}/result/viewresult`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${jwtToken}`
                        },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();
                    if (!response.ok || !result.status) {
                        throw new Error(result.message || `HTTP error! Status: ${response.status}`);
                    }
                    if (!result.data) {
                        throw new Error('No result data found in the response.');
                    }
                    populateResultData(result.data);
                } catch (error) {
                    console.error('API Error:', error);
                    showDialog('Request Failed', error.message || 'An unknown error occurred.');
                } finally {
                    showLoader(false);
                }
            });

            const populateResultData = (data) => {
                const display = (val) => val == null || val === '' ? '-' : val;
                const isInternal = data.result.examsource === 'INTERNAL';

                // Populate Headers
                document.getElementById('collegeName').textContent = display(data.college?.collegeName);
                document.getElementById('studentName').textContent = display(data.student.fullName);
                document.getElementById('EnrollmentNO').textContent = display(data.student.enrollmentNumber);
                document.getElementById('seatNo').textContent = display(data.result.seatNumber);
                document.getElementById('semesterName').textContent = display(data.semester.semesterName);
                document.getElementById('examName').textContent = display(data.examType.examName);

                // Populate Profile Image
                const profileImg = document.getElementById('profileImage');
                if (data.student.profileImage) {
                    profileImg.src = data.student.profileImage;
                } else {
                    const initial = data.student.firstName ? data.student.firstName.charAt(0).toUpperCase() : '?';
                    profileImg.src = `https://placehold.co/150x150/374151/E0E2E5?text=${initial}`;
                }

                // Dynamically build table structure based on exam source
                const tableHead = document.getElementById('marks-table-head');
                const tableFoot = document.getElementById('marks-table-foot');

                if (isInternal) {
                    tableHead.innerHTML = `
                        <tr>
                            <th rowspan="2">Subject Name</th>
                            <th colspan="2" class="text-center">Internal</th>
                            <th colspan="2" class="text-center">Total</th>
                        </tr>
                        <tr>
                            <th class="text-center">Max/Min</th>
                            <th class="text-center">Obt.</th>
                            <th class="text-center">Max/Min</th>
                            <th class="text-center">Obt.</th>
                        </tr>
                    `;
                    tableFoot.innerHTML = `
                         <tr>
                            <td class="text-right">Total</td>
                            <td id="totalSEEMaxMin" class="text-center"></td>
                            <td id="totalSEEObt" class="text-center"></td>
                            <td id="grandTotalMaxMin" class="text-center"></td>
                            <td id="grandTotalObt" class="text-center"></td>
                        </tr>
                    `;
                } else {
                    tableHead.innerHTML = `
                        <tr>
                            <th rowspan="2">Code</th>
                            <th rowspan="2">Subject Name</th>
                            <th rowspan="2" class="text-center">Credit</th>
                            <th rowspan="2" class="text-center">Grade</th>
                            <th colspan="2" class="text-center">CCE</th>
                            <th colspan="2" class="text-center">SEE</th>
                            <th colspan="2" class="text-center">Total</th>
                        </tr>
                        <tr>
                            <th class="text-center">Max/Min</th>
                            <th class="text-center">Obt.</th>
                            <th class="text-center">Max/Min</th>
                            <th class="text-center">Obt.</th>
                            <th class="text-center">Max/Min</th>
                            <th class="text-center">Obt.</th>
                        </tr>
                    `;
                    tableFoot.innerHTML = `
                        <tr>
                            <td colspan="2" class="text-right">Total</td>
                            <td id="totalCredit" class="text-center"></td>
                            <td></td>
                            <td id="totalCCEMaxMin" class="text-center"></td>
                            <td id="totalCCEObt" class="text-center"></td>
                            <td id="totalSEEMaxMin" class="text-center"></td>
                            <td id="totalSEEObt" class="text-center"></td>
                            <td id="grandTotalMaxMin" class="text-center"></td>
                            <td id="grandTotalObt" class="text-center"></td>
                        </tr>
                    `;
                }

                // Populate Table Body
                const marksBody = document.getElementById('marks-body');
                marksBody.innerHTML = '';
                let totalCredits = 0,
                    grandTotalObt = 0,
                    grandTotalMax = 0;

                const isFail = (obt, minStr) => {
                    if (obt === null || obt === undefined || minStr === null || minStr === undefined) return false;
                    const obtStr = String(obt);
                    const min = parseInt(minStr.split('/')[1], 10);
                    if (/[^0-9]/.test(obtStr)) {
                        return true; // Contains non-numeric chars like AOO, ZOO
                    }
                    const obtNum = parseInt(obtStr, 10);
                    return obtNum < min;
                };

                data.subjects.forEach(subject => {
                    totalCredits += Number(subject.credit) || 0;
                    grandTotalObt += Number(subject.total_obtained) || 0;
                    const maxMin = subject.total_max_min ? subject.total_max_min.split('/')[0] : 0;
                    grandTotalMax += Number(maxMin) || 0;

                    const cceFailClass = isFail(subject.cce_obtained, subject.cce_max_min) ? 'marks-fail' : '';
                    const seeFailClass = isFail(subject.see_obtained, subject.see_max_min) ? 'marks-fail' : '';
                    const totalFailClass = isFail(subject.total_obtained, subject.total_max_min) ? 'marks-fail' : '';


                    let rowContent = '';
                    if (isInternal) {
                        rowContent = `
                        <td>${display(subject.subject_name)}</td>
                        <td class="text-center">${display(subject.see_max_min)}</td>
                        <td class="text-center ${seeFailClass}">${display(subject.see_obtained)}</td>
                        <td class="text-center">${display(subject.total_max_min)}</td>
                        <td class="text-center ${totalFailClass}">${display(subject.total_obtained)}</td>
                        `;
                    } else { // UNIVERSITY / EXTERNAL
                        rowContent = `
                            <td>${display(subject.subject_code)}</td>
                         <td>${display(subject.subject_name)}</td>
                            <td class="text-center">${display(subject.credit)}</td>
                            <td class="text-center">${display(subject.letter_grade)}</td>
                            <td class="text-center">${display(subject.cce_max_min)}</td>
                            <td class="text-center ${cceFailClass}">${display(subject.cce_obtained)}</td>
                            <td class="text-center">${display(subject.see_max_min)}</td>
                            <td class="text-center ${seeFailClass}">${display(subject.see_obtained)}</td>
                            <td class="text-center">${display(subject.total_max_min)}</td>
                            <td class="text-center ${totalFailClass}">${display(subject.total_obtained)}</td>
                         `;
                    }

                    marksBody.innerHTML += `<tr>${rowContent}</tr>`;
                });

                // Populate Footer Totals
                if (!isInternal) {
                    document.getElementById('totalCredit').textContent = display(totalCredits);
                    document.getElementById('totalCCEObt').textContent = display(data.result.total_cce_obt);
                    document.getElementById('totalCCEMaxMin').textContent = display(data.result.total_cce_max_min);
                }
                document.getElementById('totalSEEObt').textContent = display(data.result.total_see_obt);
                document.getElementById('totalSEEMaxMin').textContent = display(data.result.total_see_max_min);
                document.getElementById('grandTotalObt').textContent = display(data.result.total_marks_obt);
                document.getElementById('grandTotalMaxMin').textContent = display(data.result.total_marks_max_min);

                // Populate Summary Boxes
                const sgpaContainer = document.getElementById('sgpaContainer');
                const rankContainer = document.getElementById('rankContainer');
                const rankTitle = document.getElementById('rankTitle');
                const rankValue = document.getElementById('rank');

                // Reset visibility
                sgpaContainer.classList.remove('hidden');
                rankContainer.classList.add('hidden');

                if (isInternal) {
                    sgpaContainer.classList.add('hidden');
                    if (data["Class Rank"] && data["Total Students"]) {
                        rankTitle.textContent = "Class Rank";
                        rankValue.textContent = `${data["Class Rank"]} / ${data["Total Students"]}`;
                        rankContainer.classList.remove('hidden');
                    }
                } else { // University
                    document.getElementById('sgpa').textContent = display(data.result.sgpa);
                    if (data["University Rank"] && data["Total Students"]) {
                        rankTitle.textContent = "University Rank";
                        rankValue.textContent = `${data["University Rank"]} / ${data["Total Students"]}`;
                        rankContainer.classList.remove('hidden');
                    }
                }

                const percentage = grandTotalMax > 0 ? ((grandTotalObt / grandTotalMax) * 100).toFixed(2) + '%' : 'N/A';
                document.getElementById('percentage').textContent = percentage;

                const resultStatusEl = document.getElementById('resultStatus');
                const resultContainer = document.getElementById('resultStatusContainer');
                resultStatusEl.textContent = display(data.result.result); // Updated key from final_result to result
                resultContainer.className = 'summary-box summary-result';
                resultContainer.classList.add(data.result.result?.toLowerCase() === 'pass' ? 'pass' : 'fail');

                showResultOverlay();
            };

            const downloadResultAsPDF = () => {
                const printableArea = document.querySelector('.printable-area');
                const studentName = document.getElementById('studentName').textContent.trim().replace(/\s+/g, '_') || 'student';
                const seatNo = document.getElementById('seatNo').textContent.trim() || 'seatno';
                const filename = `Result_${studentName}_${seatNo}.pdf`;

                const originalWidth = printableArea.style.width;
                printableArea.style.width = '1024px';
                document.body.classList.add('pdf-export-mode');

                const opt = {
                    margin: [0.10, 0.45, 0.25, 0.05],
                    filename: filename,
                    image: {
                        type: 'jpeg',
                        quality: 1.0
                    },
                    html2canvas: {
                        scale: 2.5,
                        useCORS: true,
                        backgroundColor: null
                    },
                    jsPDF: {
                        unit: 'in',
                        format: 'a4',
                        orientation: 'landscape'
                    }
                };

                html2pdf().from(printableArea).set(opt).save().then(() => {
                    printableArea.style.width = originalWidth;
                    document.body.classList.remove('pdf-export-mode');
                });
            };

            downloadBtn.addEventListener('click', downloadResultAsPDF);
        });
    </script>
</body>

</html>
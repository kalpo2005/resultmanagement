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
            --primary-dark: #1F2937;
            --secondary-dark: #374151;
            --border-dark: #30363d;
            --text-light: #F9FAFB;
            --text-muted: #9CA3AF;
            --accent-color: #4F46E5;
            --accent-hover: #4338CA;
            --success-color: #16A34A;
            --error-color: #DC2626;
            --info-color: #3B82F6;
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

        .d-none {
            display: none !important;
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
            color: var(--text-light);
        }

        .btn {
            background-color: var(--accent-color);
            color: white;
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
            display: none;
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
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
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

        .result-summary {
            display: grid;
            gap: 1.5rem;
            margin-top: 2rem;
            grid-template-columns: repeat(3, 1fr);
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
            background: linear-gradient(145deg, rgba(79, 70, 229, 0.4), rgba(99, 102, 241, 0.2));
            border: 1px solid rgba(99, 102, 241, 0.5);
        }

        .summary-sgpa p:first-child {
            color: #A5B4FC;
        }

        .summary-result.pass {
            background: linear-gradient(145deg, rgba(22, 163, 74, 0.4), rgba(34, 197, 94, 0.2));
            border: 1px solid rgba(34, 197, 94, 0.5);
        }

        .summary-result.pass p:first-child {
            color: #86EFAC;
        }

        .summary-result.fail {
            background: linear-gradient(145deg, rgba(220, 38, 38, 0.4), rgba(239, 68, 68, 0.2));
            border: 1px solid rgba(239, 68, 68, 0.5);
        }

        .summary-result.fail p:first-child {
            color: #FCA5A5;
        }

        .summary-percentage {
            background: linear-gradient(145deg, rgba(59, 130, 246, 0.4), rgba(96, 165, 250, 0.2));
            border: 1px solid rgba(96, 165, 250, 0.5);
        }

        .summary-percentage p:first-child {
            color: #93C5FD;
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
        .pdf-export-mode .result-card {
            padding: 1.5rem;
            box-shadow: none;
            border: 1px solid #ccc;
            border-radius: 0;
            color: black !important;
        }

        .pdf-export-mode .result-card * {
            color: black !important;
            border-color: #ddd !important;
        }

        .pdf-export-mode .card-header h2 {
            font-size: 16pt;
        }

        .pdf-export-mode .card-header p {
            font-size: 10pt;
        }

        .pdf-export-mode hr {
            background-color: #ccc !important;
        }

        .pdf-export-mode .student-info-block {
            margin-bottom: 1.5rem;
            gap: 1.5rem;
        }

        .pdf-export-mode .student-photo img {
            width: 6rem;
            height: 6rem;
            border-color: #ccc !important;
        }

        .pdf-export-mode .student-details-grid {
            font-size: 9pt;
            gap: 0.5rem 2rem;
        }

        .pdf-export-mode .student-details-grid div strong {
            font-size: 9pt;
            color: #555 !important;
        }

        .pdf-export-mode .student-details-grid div span {
            font-size: 10pt;
            font-weight: bold;
        }

        .pdf-export-mode .marks-table {
            font-size: 8pt;
            width: 100%;
        }

        .pdf-export-mode .marks-table th,
        .pdf-export-mode .marks-table td {
            padding: 0.4rem 0.5rem;
            border: 1px solid #ddd;
        }

        .pdf-export-mode .marks-table thead th {
            background-color: #f2f2f2 !important;
        }

        .pdf-export-mode .marks-table tfoot td {
            background-color: #f2f2f2 !important;
        }

        .pdf-export-mode .result-summary {
            margin-top: 1.5rem;
            gap: 1rem;
        }

        .pdf-export-mode .summary-box {
            padding: 1rem;
            background: #f2f2f2 !important;
            border: 1px solid #ddd;
        }

        .pdf-export-mode .summary-box p:first-child {
            font-size: 8pt;
        }

        .pdf-export-mode .summary-box p:last-child {
            font-size: 14pt;
        }

        .pdf-export-mode .action-buttons {
            display: none !important;
        }

        /* Hide scrollbars inside PDF */
        .pdf-export-mode .table-container {
            overflow: visible !important;
            /* allow full table width */
        }

        .pdf-export-mode .table-container::-webkit-scrollbar {
            display: none !important;
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
                        <input type="radio" id="classA" name="studentClass" value="A"><label for="classA">A</label>
                        <input type="radio" id="classB" name="studentClass" value="B"><label for="classB">B</label>
                        <input type="radio" id="classC" name="studentClass" value="C"><label for="classC">C</label>
                        <input type="radio" id="classD" name="studentClass" value="D"><label for="classD">D</label>
                        <input type="radio" id="classD" name="studentClass" value="D" checked><label for="classD">E</label>
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
                    <div style="text-align: center; width: 100%;">
                        <h2 id="collegeName" style="margin-top: 4px; font-weight: 600;"></h2>
                        <h4>Statement of Marks</h4>
                        <p id="examName"></p>
                    </div>
                    <div class="action-buttons">
                        <button id="downloadBtn" class="btn-icon d-none" title="Download PDF">
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
                    </div>
                </div>
                <div class="table-container">
                    <table class="marks-table">
                        <thead>
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
                        </thead>
                        <tbody id="marks-body"></tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" class="text-right">Total</td>
                                <td id="totalCredit" class="text-center"></td>
                                <td id="totalCCE" class="text-center"></td>

                                <td id="ceeMinMax" class="text-center"></td>
                                <td id="ceeObtain" class="text-center"></td>

                                <td id="seeMinMax" class="text-center"></td>
                                <td id="seeObtain" class="text-center"></td>

                                <td id="totalMinMax" class="text-center"></td>
                                <td id="totalObtain" class="text-center"></td>

                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="result-summary">
                    <div class="summary-box summary-sgpa">
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
                document.getElementById('studentName').textContent = data.student.fullName || 'N/A';
                document.getElementById('seatNo').textContent = data.student.enrollmentNumber || 'N/A';
                document.getElementById('semesterName').textContent = data.semester.semesterName || 'N/A';
                document.getElementById('examName').textContent = data.examType.examName || 'N/A';
                document.getElementById('collegeName').textContent = data.college?.collegeName || 'N/A';

                const profileImg = document.getElementById('profileImage');
                if (data.student.profileImage) {
                    profileImg.src = data.student.profileImage;
                } else {
                    const initial = data.student.firstName ? data.student.firstName.charAt(0).toUpperCase() : '?';
                    profileImg.src = `https://placehold.co/150x150/374151/E0E2E5?text=${initial}`;
                }

                const display = (val) => val == null || val === '' ? '-' : val;


                const marksBody = document.getElementById('marks-body');
                marksBody.innerHTML = '';
                let totalCredits = 0,
                    totalCCEObt = 0,
                    totalSEEObt = 0,
                    grandTotalObt = 0,
                    grandTotalMax = 0;

                data.subjects.forEach(subject => {
                    totalCredits += Number(subject.credit) || 0;
                    totalCCEObt += Number(subject.cce_obtained) || 0;
                    totalSEEObt += Number(subject.see_obtained) || 0;
                    grandTotalObt += Number(subject.total_obtained) || 0;
                    grandTotalMax += Number(subject.total_max_min.split('/')[0]) || 0;

                    marksBody.innerHTML += `
                   <tr>
                        <td>${display(subject.subject_code)}</td>
                        <td>${display(subject.subject_name)}</td>
                     <td class="text-center">${display(subject.credit)}</td>
                        <td class="text-center">${display(subject.letter_grade)}</td>
                      <td class="text-center">${display(subject.cce_max_min)}</td>
                      <td class="text-center">${display(subject.cce_obtained)}</td>
                      <td class="text-center">${display(subject.see_max_min)}</td>
                      <td class="text-center">${display(subject.see_obtained)}</td>
                      <td class="text-center">${display(subject.total_max_min)}</td>
                      <td class="text-center">${display(subject.total_obtained)}</td>
                  </tr>`;
                });

                document.getElementById('totalCredit').textContent = totalCredits;
                // document.getElementById('totalCCE').textContent = totalCCEObt;
                // document.getElementById('totalSEE').textContent = totalSEEObt;
                document.getElementById('ceeObtain').textContent = data.result.total_cce_obt || totalCCEObt || '-';
                document.getElementById('ceeMinMax').textContent = data.result.total_cce_max_min || '-';
                document.getElementById('seeObtain').textContent = data.result.total_see_obt || totalSEEObt || '-';
                document.getElementById('seeMinMax').textContent = data.result.total_see_max_min || '-';
                document.getElementById('ceeMinMax').textContent = data.result.total_cce_max_min || '-';
                // document.getElementById('totalObtain').textContent = data.result.total_marks_obt || '-';
                // document.getElementById('totalMinMax').textContent = data.result.total_marks_max_min || '-';
                document.getElementById('totalObtain').textContent = data.result.total_marks_obt || '-';
                document.getElementById('totalMinMax').textContent = data.result.total_marks_max_min || '-';
                // document.getElementById('grandTotal').textContent = grandTotalObt;

                document.getElementById('sgpa').textContent = data.result.sgpa || 'N/A';

                const percentage = grandTotalMax > 0 ? ((grandTotalObt / grandTotalMax) * 100).toFixed(2) + '%' : 'N/A';
                document.getElementById('percentage').textContent = percentage;

                const resultStatusEl = document.getElementById('resultStatus');
                const resultContainer = document.getElementById('resultStatusContainer');
                resultStatusEl.textContent = data.result.final_result || 'N/A';
                resultContainer.className = 'summary-box summary-result';
                resultContainer.classList.add(data.result.final_result?.toLowerCase() === 'pass' ? 'pass' : 'fail');

                showResultOverlay();
            };
            const downloadResultAsPDF = () => {
                const sourceDiv = document.getElementById('result-section');

                // 1️⃣ Clone the div (deep clone with children)
                const clone = sourceDiv.cloneNode(true);
                clone.classList.add('result-section-clone');

                // 2️⃣ Remove the action-buttons inside the clone
                const actions = clone.querySelector('.action-buttons');
                if (actions) actions.remove();

                // 3️⃣ Create a temporary container for clean export
                const tempContainer = document.createElement('div');
                tempContainer.style.position = 'absolute';
                tempContainer.style.left = '-9999px';
                tempContainer.appendChild(clone);
                document.body.appendChild(tempContainer);

                // 4️⃣ Prepare filename
                const studentName = document.getElementById('studentName')?.textContent.trim().replace(/\s+/g, '_') || 'student';
                const seatNo = document.getElementById('seatNo')?.textContent.trim() || 'seatno';
                const filename = `Result_${studentName}_${seatNo}.pdf`;

                // 5️⃣ Build html2pdf options (no fixed format to avoid extra blank space)
                const opt = {
                    margin: 0,
                    filename: filename,
                    image: {
                        type: 'jpeg',
                        quality: 0.95
                    },
                    html2canvas: {
                        scale: 2,
                        useCORS: true
                    },
                    jsPDF: {
                        unit: 'px'
                    } // ⚡ don't set format here – let html2pdf use canvas size
                };

                // 6️⃣ Generate the PDF and resize page to exact canvas
                html2pdf().set(opt).from(clone).toPdf().get('pdf').then(pdf => {
                    const canvas = document.querySelector('.html2pdf__canvas');
                    if (canvas) {
                        const width = canvas.offsetWidth;
                        const height = canvas.offsetHeight;
                        pdf.internal.pageSize.width = width;
                        pdf.internal.pageSize.height = height;
                    }
                }).save().then(() => {
                    // 7️⃣ Clean up the temporary container
                    document.body.removeChild(tempContainer);
                });
            };


            downloadBtn.addEventListener('click', downloadResultAsPDF);
        });
    </script>
</body>

</html>
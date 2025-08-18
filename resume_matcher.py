import pdfplumber
import ollama  # NEW
import os

# ---- Step 1: Extract text from resume PDF ----
def extract_text_from_pdf(file_path):
    with pdfplumber.open(file_path) as pdf:
        return " ".join(page.extract_text() for page in pdf.pages if page.extract_text())

# ---- Step 2: Summarize resume using Ollama ----
def summarize_resume(text):
    prompt = f"""
Extract the following from this resume:
- Full Name
- Email (if present)
- Key Skills (as a list)
- Total estimated years of experience
- Highest education
- A 1-2 sentence summary

Resume Text:
{text}
"""

    response = ollama.chat(
        model='mistral',
        messages=[
            {'role': 'user', 'content': prompt}
        ]
    )

    return response['message']['content']

# ---- Step 3: Match candidate to job role ----
def match_candidate(candidate_skills, experience_years, job):
    score = 0
    matched_skills = [skill for skill in candidate_skills if skill in job["required_skills"]]
    score += len(matched_skills) * 10

    if experience_years >= job["min_experience"]:
        score += 20

    return score, matched_skills

# ---- Sample Job Role ----
job_role = {
    "title": "Backend Engineer",
    "required_skills": ["Python", "Django", "SQL", "REST APIs"],
    "min_experience": 2,
    "culture_keywords": ["team player", "agile", "fast-paced"]
}

# ---- Main Program ----
if __name__ == "__main__":
    resume_path = "C:/Users/OWNER/Downloads/BLANK_SPACE_ANG YEE SIEW.pdf"
    print(f"📄 Reading: {resume_path}")
    resume_text = extract_text_from_pdf(resume_path)

    print("🧠 Sending to Mistral (local Ollama) for summarization...")
    summary = summarize_resume(resume_text)
    print("✅ Resume Summary:\n")
    print(summary)

    # TEMP: mock extracted info (until you parse the summary)
    candidate_skills = ["Python", "Django", "SQL"]
    experience_years = 3

    score, matched = match_candidate(candidate_skills, experience_years, job_role)

    print("\n🎯 Job Match Results:")
    print(f"Score: {score}/100")
    print(f"Matched Skills: {matched}")
